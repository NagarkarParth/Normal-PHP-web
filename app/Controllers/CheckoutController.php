<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Cart;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Mailer;
use App\Models\Order;
use App\Models\Setting;
use App\Payments\GatewayFactory;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        $totals = Cart::totals();

        if ($totals['items'] === []) {
            Flash::info('Your cart is empty.');
            redirect('/products');
        }

        $gateway = GatewayFactory::make();

        $this->render('pages/checkout', [
            'pageTitle' => 'Checkout',
            'totals'    => $totals,
            'gateway'   => $gateway,
            'user'      => $user,
        ]);
    }

    public function place(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $totals = Cart::totals();

        if ($totals['items'] === []) {
            Flash::error('Your cart is empty.');
            redirect('/products');
        }

        $gateway = GatewayFactory::make();
        $orderId = Order::create([
            'order_number'   => Order::nextNumber(),
            'user_id'        => (int) $user['id'],
            'customer_name'  => (string) $user['name'],
            'customer_email' => (string) $user['email'],
            'subtotal'       => $totals['subtotal'],
            'discount'       => $totals['discount'],
            'tax'            => $totals['tax'],
            'tax_rate'       => $totals['tax_rate'],
            'total'          => $totals['total'],
            'currency'       => (string) Setting::get('currency', 'INR'),
            'coupon_id'      => $totals['coupon'] === null ? null : (int) $totals['coupon']['id'],
            'coupon_code'    => $totals['coupon'] === null ? null : (string) $totals['coupon']['code'],
            'gateway'        => $gateway->key(),
        ], $totals['items']);

        $order = Order::find($orderId);
        if ($order === null) {
            Flash::error('The order could not be created. Please try again.');
            redirect('/cart');
        }

        $result = $gateway->start($order);

        if ($result->mode === 'failed') {
            Order::markFailed($orderId, $result->error);
            Flash::error($result->error);
            redirect('/checkout');
        }

        if (isset($result->options['gateway_ref'])) {
            Order::update($orderId, ['gateway_ref' => (string) $result->options['gateway_ref']]);
        }

        if ($result->mode === 'instant') {
            $confirmation = $gateway->confirm($order, []);
            $this->completeOrder($orderId, $confirmation);
        }

        if ($result->mode === 'redirect') {
            if (isset($_SESSION['stripe_session_' . $orderId])) {
                Order::update($orderId, ['gateway_ref' => (string) $_SESSION['stripe_session_' . $orderId]]);
            }
            if (isset($_SESSION['paypal_order_' . $orderId])) {
                Order::update($orderId, ['gateway_ref' => (string) $_SESSION['paypal_order_' . $orderId]]);
            }
            redirect($result->redirectUrl);
        }

        $this->render('pages/pay', [
            'pageTitle' => 'Completing payment',
            'order'     => Order::find($orderId),
            'checkout'  => $result->options,
            'gateway'   => $gateway,
        ]);
    }

    /** Razorpay posts its handler payload back here. */
    public function confirm(string $gatewayKey): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $order = Order::findForUser((int) $this->request->input('order_id', 0), (int) $user['id']);

        if ($order === null) {
            Flash::error('Order not found.');
            redirect('/orders');
        }

        $gateway = GatewayFactory::make($gatewayKey);
        $confirmation = $gateway->confirm($order, $this->request->all());
        $this->completeOrder((int) $order['id'], $confirmation);
    }

    /** Stripe and PayPal send the buyer back to this URL. */
    public function returnFromGateway(string $gatewayKey): void
    {
        $user = $this->requireAuth();
        $order = Order::findForUser((int) $this->request->query('order', 0), (int) $user['id']);

        if ($order === null) {
            Flash::error('Order not found.');
            redirect('/orders');
        }

        if ($order['payment_status'] === 'paid') {
            redirect('/orders/' . $order['id']);
        }

        $gateway = GatewayFactory::make($gatewayKey);
        $confirmation = $gateway->confirm($order, $this->request->all());
        $this->completeOrder((int) $order['id'], $confirmation);
    }

    public function cancel(string $orderId): void
    {
        $user = $this->requireAuth();
        $order = Order::findForUser((int) $orderId, (int) $user['id']);
        if ($order !== null && $order['payment_status'] === 'pending') {
            Order::markFailed((int) $order['id'], 'Cancelled by customer at the gateway.');
        }

        Flash::info('Payment cancelled. Your cart is still saved.');
        redirect('/cart');
    }

    /** @param array{success: bool, transaction_id: string, message: string} $confirmation */
    private function completeOrder(int $orderId, array $confirmation): never
    {
        if (!$confirmation['success']) {
            Order::markFailed($orderId, $confirmation['message']);
            $order = Order::find($orderId);
            if ($order !== null) {
                Mailer::send(
                    (string) $order['customer_email'],
                    'Payment failed for order ' . $order['order_number'],
                    '<p>We could not confirm your payment. No amount has been captured.</p>',
                    'order_failed'
                );
            }
            Flash::error($confirmation['message']);
            redirect('/checkout');
        }

        Order::markPaid($orderId, $confirmation['transaction_id']);
        Cart::clear();

        $order = Order::find($orderId);
        if ($order !== null) {
            Mailer::send(
                (string) $order['customer_email'],
                'Your order ' . $order['order_number'] . ' is confirmed',
                '<p>Thank you! Your downloads are ready at ' . url('orders/' . $orderId) . '</p>',
                'order_paid'
            );
        }

        Flash::success('Payment successful. Your downloads are ready.');
        redirect('/orders/' . $orderId . '?paid=1');
    }
}
