<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\Order;
use App\Models\Setting;

final class OrderController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();

        $this->render('pages/orders', [
            'pageTitle' => 'Orders & downloads',
            'orders'    => Order::forUser((int) $user['id']),
            'purchases' => Order::purchasedProducts((int) $user['id']),
        ]);
    }

    public function show(string $id): void
    {
        $user = $this->requireAuth();
        $order = Order::findForUser((int) $id, (int) $user['id']);

        if ($order === null) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Order not found']);

            return;
        }

        $this->render('pages/order', [
            'pageTitle' => 'Order ' . $order['order_number'],
            'order'     => $order,
            'items'     => Order::items((int) $order['id']),
        ]);
    }

    public function invoice(string $id): void
    {
        $user = $this->requireAuth();
        $order = Order::findForUser((int) $id, (int) $user['id']);

        if ($order === null) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Order not found']);

            return;
        }

        $this->render('pages/invoice', [
            'pageTitle' => 'Invoice ' . $order['order_number'],
            'order'     => $order,
            'items'     => Order::items((int) $order['id']),
            'branding'  => Setting::branding(),
        ], 'blank');
    }

    /**
     * Streams the digital file. The token is unguessable, ownership is re-checked on
     * every hit and expired links stop working when download expiry is enabled.
     */
    public function download(string $token): void
    {
        $user = $this->requireAuth();
        $item = Order::itemByToken($token);

        if ($item === null || (int) $item['user_id'] !== (int) $user['id']) {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Download not found']);

            return;
        }

        if ($item['payment_status'] !== 'paid') {
            Flash::error('This order has not been paid yet.');
            redirect('/orders');
        }

        if ($item['expires_at'] !== null && strtotime((string) $item['expires_at']) < time()) {
            Flash::error('This download link has expired. Contact support for a fresh link.');
            redirect('/orders');
        }

        $storage = rtrim((string) Config::get('storage.products'), '/\\');
        $path = (string) ($item['file_path'] ?? '');
        $absolute = $path === '' ? '' : $storage . DIRECTORY_SEPARATOR . basename($path);

        if ($absolute === '' || !is_file($absolute)) {
            Flash::error('The file for this product is missing. Our team has been notified.');
            redirect('/orders');
        }

        Order::logDownload((int) $item['id'], Auth::id(), $this->request->ip());

        $filename = (string) ($item['file_name'] ?? basename($absolute));
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($absolute));
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($absolute);
        exit;
    }
}
