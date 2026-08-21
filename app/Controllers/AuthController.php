<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Mailer;
use App\Core\Validator;
use App\Models\PasswordReset;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->render('auth/login', ['pageTitle' => 'Sign in'], 'auth');
    }

    public function login(): void
    {
        $this->verifyCsrf();
        $email = (string) $this->request->input('email', '');
        $password = (string) $this->request->input('password', '');

        $validator = Validator::make($this->request->all())
            ->required('email', 'Email')->email('email', 'Email')
            ->required('password', 'Password');

        if ($validator->fails()) {
            $this->withInput($this->request->all());
            Flash::error((string) $validator->firstError());
            redirect('/login');
        }

        if (Auth::attempt($email, $password) === null) {
            $this->withInput($this->request->all());
            Flash::error('Those credentials do not match our records, or the account is blocked.');
            redirect('/login');
        }

        $intended = $_SESSION['_intended'] ?? null;
        unset($_SESSION['_intended']);

        Flash::success('Welcome back!');
        redirect($intended ?? (Auth::isAdmin() ? '/admin' : '/'));
    }

    public function showSignup(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        $this->render('auth/signup', ['pageTitle' => 'Create your account'], 'auth');
    }

    public function signup(): void
    {
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')->email('email', 'Email')
            ->required('password', 'Password')->min('password', 8, 'Password')
            ->matches('password_confirmation', 'password', 'Password confirmation')
            ->condition('email', !User::emailExists((string) ($data['email'] ?? '')), 'An account with that email already exists.');

        if ($validator->fails()) {
            $this->withInput($data);
            Flash::error((string) $validator->firstError());
            redirect('/signup');
        }

        $userId = User::create((string) $data['name'], (string) $data['email'], (string) $data['password']);
        Auth::login($userId);

        Mailer::send(
            (string) $data['email'],
            'Welcome to your digital storefront',
            '<p>Your account is ready. Browse products at ' . url('products') . '</p>',
            'welcome'
        );

        Flash::success('Account created. Happy shopping!');
        redirect('/');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Auth::logout();
        Flash::info('You have been signed out.');
        redirect('/');
    }

    public function showForgot(): void
    {
        $this->render('auth/forgot', ['pageTitle' => 'Reset your password'], 'auth');
    }

    public function forgot(): void
    {
        $this->verifyCsrf();
        $email = (string) $this->request->input('email', '');
        $user = User::findByEmail($email);

        if ($user !== null) {
            $token = PasswordReset::issue((int) $user['id']);
            Mailer::send(
                $email,
                'Reset your password',
                '<p>Use this link within one hour to choose a new password:</p><p>' . url('reset/' . $token) . '</p>'
            );
        }

        // Always the same response so the form cannot be used to enumerate accounts.
        Flash::success('If that email is registered, a reset link is on its way.');
        redirect('/login');
    }

    public function showReset(string $token): void
    {
        if (PasswordReset::findValid($token) === null) {
            Flash::error('That reset link is invalid or has expired.');
            redirect('/forgot');
        }

        $this->render('auth/reset', ['pageTitle' => 'Choose a new password', 'token' => $token], 'auth');
    }

    public function reset(string $token): void
    {
        $this->verifyCsrf();
        $reset = PasswordReset::findValid($token);

        if ($reset === null) {
            Flash::error('That reset link is invalid or has expired.');
            redirect('/forgot');
        }

        $validator = Validator::make($this->request->all())
            ->required('password', 'Password')->min('password', 8, 'Password')
            ->matches('password_confirmation', 'password', 'Password confirmation');

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/reset/' . $token);
        }

        User::updatePassword((int) $reset['user_id'], (string) $this->request->input('password'));
        PasswordReset::consume((int) $reset['id']);

        Flash::success('Password updated. You can sign in now.');
        redirect('/login');
    }
}
