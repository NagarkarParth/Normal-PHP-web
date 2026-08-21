<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Order;
use App\Models\User;

final class UserAdminController extends Controller
{
    private const PER_PAGE = 20;

    public function index(): void
    {
        $this->requireAdmin('users');
        $page = max(1, (int) $this->request->query('page', 1));
        $search = (string) $this->request->query('q', '');
        $total = User::countAll($search);

        $this->render('admin/users/index', [
            'pageTitle' => 'Users',
            'users'     => User::paginate($search, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'total'     => $total,
            'page'      => $page,
            'pages'     => (int) ceil($total / self::PER_PAGE),
            'search'    => $search,
        ], 'admin');
    }

    public function show(string $id): void
    {
        $this->requireAdmin('users');
        $account = User::find((int) $id);

        if ($account === null) {
            Flash::error('User not found.');
            redirect('/admin/users');
        }

        $this->render('admin/users/show', [
            'pageTitle' => $account['name'],
            'account'   => $account,
            'orders'    => Order::forUser((int) $account['id']),
        ], 'admin');
    }

    public function toggleBlock(string $id): void
    {
        $this->requireAdmin('users');
        $this->verifyCsrf();
        $account = User::find((int) $id);

        if ($account === null) {
            Flash::error('User not found.');
            redirect('/admin/users');
        }

        if ((int) $account['id'] === Auth::id()) {
            Flash::error('You cannot block your own account.');
            redirect('/admin/users/' . $id);
        }

        $blocked = (int) $account['is_blocked'] === 0;
        User::setBlocked((int) $id, $blocked);

        Flash::success($blocked ? 'User blocked.' : 'User unblocked.');
        redirect('/admin/users/' . $id);
    }

    public function updateRole(string $id): void
    {
        $this->requireAdmin('users');
        $this->verifyCsrf();
        $role = (string) $this->request->input('role', 'customer');

        if (!in_array($role, ['customer', 'editor', 'super_admin'], true)) {
            Flash::error('Unknown role.');
            redirect('/admin/users/' . $id);
        }

        if ((int) $id === Auth::id()) {
            Flash::error('You cannot change your own role.');
            redirect('/admin/users/' . $id);
        }

        User::update((int) $id, ['role' => $role]);
        Flash::success('Role updated.');
        redirect('/admin/users/' . $id);
    }

    public function store(): void
    {
        $this->requireAdmin('users');
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')->email('email', 'Email')
            ->required('password', 'Password')->min('password', 8, 'Password')
            ->condition('email', !User::emailExists((string) ($data['email'] ?? '')), 'That email already exists.');

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/admin/users');
        }

        $role = in_array($data['role'] ?? '', ['customer', 'editor', 'super_admin'], true) ? (string) $data['role'] : 'customer';
        User::create((string) $data['name'], (string) $data['email'], (string) $data['password'], $role);

        Flash::success('Team member created.');
        redirect('/admin/users');
    }
}
