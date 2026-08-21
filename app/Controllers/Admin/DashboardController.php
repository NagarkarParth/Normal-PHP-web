<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Report;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $this->render('admin/dashboard', [
            'pageTitle'    => 'Dashboard',
            'stats'        => Report::dashboard(),
            'daily'        => Report::dailySales(14),
            'topProducts'  => Report::topProducts(5),
            'recentOrders' => Report::recentOrders(8),
        ], 'admin');
    }
}
