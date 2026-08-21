<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

abstract class Controller
{
    protected View $view;

    public function __construct(protected Request $request)
    {
        $this->view = new View();
    }

    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = [], string $layout = 'app'): void
    {
        $shared = [
            'flashes'    => Flash::pull(),
            'user'       => Auth::user(),
            'cartCount'  => Cart::count(),
            'branding'   => Setting::branding(),
            'pageTitle'  => $data['pageTitle'] ?? Setting::branding()['site_name'],
        ];

        echo $this->view->render($template, array_merge($shared, $data), $layout);
        unset($_SESSION['_old']);
    }

    /** @param array<string, mixed> $payload */
    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function verifyCsrf(): void
    {
        if (!Csrf::check((string) $this->request->input('_token', ''))) {
            http_response_code(419);
            Flash::error('Your session expired. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    /** @param array<string, mixed> $input */
    protected function withInput(array $input): void
    {
        unset($input['_token'], $input['password'], $input['password_confirmation']);
        $_SESSION['_old'] = $input;
    }

    protected function requireAuth(): array
    {
        $user = Auth::user();
        if ($user === null) {
            $_SESSION['_intended'] = $this->request->path();
            Flash::error('Please sign in to continue.');
            redirect('/login');
        }

        return $user;
    }

    protected function requireAdmin(?string $permission = null): array
    {
        $user = $this->requireAuth();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            Flash::error('You do not have access to the admin area.');
            redirect('/');
        }
        if ($permission !== null && !Auth::can($permission)) {
            http_response_code(403);
            Flash::error('Your role does not allow that action.');
            redirect('/admin');
        }

        return $user;
    }

    protected function back(string $fallback = '/'): never
    {
        redirect($_SERVER['HTTP_REFERER'] ?? $fallback);
    }
}
