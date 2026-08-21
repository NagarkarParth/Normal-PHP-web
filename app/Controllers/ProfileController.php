<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Order;
use App\Models\User;

final class ProfileController extends Controller
{
    public function show(): void
    {
        $user = $this->requireAuth();

        $this->render('pages/profile', [
            'pageTitle' => 'Your profile',
            'profile'   => $user,
            'orders'    => array_slice(Order::forUser((int) $user['id']), 0, 5),
        ]);
    }

    public function update(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')->email('email', 'Email')
            ->condition('email', !User::emailExists((string) ($data['email'] ?? ''), (int) $user['id']), 'That email is already in use.');

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/profile');
        }

        User::update((int) $user['id'], [
            'name'  => (string) $data['name'],
            'email' => strtolower(trim((string) $data['email'])),
            'phone' => (string) ($data['phone'] ?? ''),
        ]);

        Flash::success('Profile updated.');
        redirect('/profile');
    }

    public function updatePassword(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('current_password', 'Current password')
            ->required('password', 'New password')->min('password', 8, 'New password')
            ->matches('password_confirmation', 'password', 'Password confirmation')
            ->condition(
                'current_password',
                password_verify((string) ($data['current_password'] ?? ''), (string) $user['password_hash']),
                'Your current password is incorrect.'
            );

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/profile');
        }

        User::updatePassword((int) $user['id'], (string) $data['password']);
        Auth::logout();

        Flash::success('Password changed. Please sign in again.');
        redirect('/login');
    }
}
