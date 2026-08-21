<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Uploader;
use App\Models\Setting;
use App\Payments\GatewayFactory;

final class SettingAdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin('settings');

        $this->render('admin/settings', [
            'pageTitle' => 'Settings',
            'settings'  => Setting::all(),
            'gateways'  => GatewayFactory::all(),
        ], 'admin');
    }

    public function saveBranding(): void
    {
        $this->requireAdmin('settings');
        $this->verifyCsrf();

        $values = [
            'site_name'     => (string) $this->request->input('site_name', 'YBT Digital'),
            'site_tagline'  => (string) $this->request->input('site_tagline', ''),
            'footer_text'   => (string) $this->request->input('footer_text', ''),
            'support_email' => (string) $this->request->input('support_email', ''),
        ];

        $logo = $this->request->file('site_logo');
        if ($logo !== null) {
            $path = Uploader::image($logo, 'branding');
            if ($path !== null) {
                $values['site_logo'] = $path;
            }
        }

        Setting::setMany($values);
        Flash::success('Branding saved.');
        redirect('/admin/settings#branding');
    }

    public function savePayments(): void
    {
        $this->requireAdmin('payments');
        $this->verifyCsrf();

        Setting::setMany([
            'payment_gateway'        => (string) $this->request->input('payment_gateway', 'offline'),
            'razorpay_key_id'        => (string) $this->request->input('razorpay_key_id', ''),
            'razorpay_key_secret'    => (string) $this->request->input('razorpay_key_secret', ''),
            'stripe_publishable_key' => (string) $this->request->input('stripe_publishable_key', ''),
            'stripe_secret_key'      => (string) $this->request->input('stripe_secret_key', ''),
            'paypal_client_id'       => (string) $this->request->input('paypal_client_id', ''),
            'paypal_secret'          => (string) $this->request->input('paypal_secret', ''),
            'paypal_mode'            => $this->request->input('paypal_mode') === 'live' ? 'live' : 'sandbox',
        ]);

        Flash::success('Payment gateway settings saved.');
        redirect('/admin/settings#payments');
    }

    public function saveTax(): void
    {
        $this->requireAdmin('settings');
        $this->verifyCsrf();

        Setting::setMany([
            'currency'             => (string) $this->request->input('currency', 'INR'),
            'tax_label'            => (string) $this->request->input('tax_label', 'GST'),
            'tax_rate'             => (float) $this->request->input('tax_rate', 0),
            'download_expiry_days' => max(0, (int) $this->request->input('download_expiry_days', 0)),
        ]);

        Flash::success('Tax and currency settings saved.');
        redirect('/admin/settings#tax');
    }

    public function saveNotifications(): void
    {
        $this->requireAdmin('settings');
        $this->verifyCsrf();

        foreach (['order_paid', 'order_failed', 'welcome', 'ticket_reply'] as $key) {
            Setting::set('notify_' . $key, $this->request->has('notify_' . $key) ? '1' : '0');
        }

        Flash::success('Email notification preferences saved.');
        redirect('/admin/settings#notifications');
    }
}
