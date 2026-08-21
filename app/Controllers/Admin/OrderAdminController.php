<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Mailer;
use App\Models\Order;

final class OrderAdminController extends Controller
{
    private const PER_PAGE = 20;

    public function index(): void
    {
        $this->requireAdmin('orders');
        $page = max(1, (int) $this->request->query('page', 1));
        $search = (string) $this->request->query('q', '');
        $status = (string) $this->request->query('status', '');
        $total = Order::adminCount($search, $status);

        $this->render('admin/orders/index', [
            'pageTitle' => 'Orders',
            'orders'    => Order::adminList($search, $status, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'total'     => $total,
            'page'      => $page,
            'pages'     => (int) ceil($total / self::PER_PAGE),
            'search'    => $search,
            'status'    => $status,
        ], 'admin');
    }

    public function show(string $id): void
    {
        $this->requireAdmin('orders');
        $order = Order::find((int) $id);

        if ($order === null) {
            Flash::error('Order not found.');
            redirect('/admin/orders');
        }

        $this->render('admin/orders/show', [
            'pageTitle' => 'Order ' . $order['order_number'],
            'order'     => $order,
            'items'     => Order::items((int) $order['id']),
        ], 'admin');
    }

    public function markPaid(string $id): void
    {
        $this->requireAdmin('orders');
        $this->verifyCsrf();
        $reference = (string) $this->request->input('transaction_id', '');
        Order::markPaid((int) $id, $reference !== '' ? $reference : 'MANUAL-' . strtoupper(bin2hex(random_bytes(3))));

        $order = Order::find((int) $id);
        if ($order !== null) {
            Mailer::send(
                (string) $order['customer_email'],
                'Your order ' . $order['order_number'] . ' is confirmed',
                '<p>Your downloads are ready at ' . url('orders/' . $order['id']) . '</p>',
                'order_paid'
            );
        }

        Flash::success('Order marked as paid and the customer was notified.');
        redirect('/admin/orders/' . $id);
    }

    public function refund(string $id): void
    {
        $this->requireAdmin('payments');
        $this->verifyCsrf();
        $order = Order::find((int) $id);

        if ($order === null) {
            Flash::error('Order not found.');
            redirect('/admin/orders');
        }

        $amount = (float) $this->request->input('amount', $order['total']);
        Order::refund((int) $id, min($amount, (float) $order['total']), (string) $this->request->input('note', ''));

        Mailer::send(
            (string) $order['customer_email'],
            'Refund processed for order ' . $order['order_number'],
            '<p>We have refunded ' . money($amount) . ' for order ' . e((string) $order['order_number']) . '.</p>'
        );

        Flash::success('Refund recorded.');
        redirect('/admin/orders/' . $id);
    }
}
