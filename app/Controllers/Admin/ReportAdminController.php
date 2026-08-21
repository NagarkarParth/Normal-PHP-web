<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Report;

final class ReportAdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin('reports');

        $this->render('admin/reports', [
            'pageTitle'   => 'Reports & analytics',
            'stats'       => Report::dashboard(),
            'daily'       => Report::dailySales(30),
            'monthly'     => Report::monthlySales(12),
            'topProducts' => Report::topProducts(10),
        ], 'admin');
    }
}
