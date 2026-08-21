<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Coupon;

final class CouponAdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin('coupons');

        $this->render('admin/coupons', [
            'pageTitle' => 'Coupons & discounts',
            'coupons'   => Coupon::all(),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin('coupons');
        $this->verifyCsrf();
        $data = $this->request->all();
        $code = strtoupper(trim((string) ($data['code'] ?? '')));

        $validator = Validator::make($data)
            ->required('code', 'Code')
            ->required('value', 'Value')->numeric('value', 'Value')
            ->condition('code', !Coupon::codeExists($code), 'That coupon code already exists.');

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/admin/coupons');
        }

        Coupon::create($this->payload($data, $code));
        Flash::success('Coupon created.');
        redirect('/admin/coupons');
    }

    public function update(string $id): void
    {
        $this->requireAdmin('coupons');
        $this->verifyCsrf();
        $data = $this->request->all();
        $code = strtoupper(trim((string) ($data['code'] ?? '')));

        if (Coupon::codeExists($code, (int) $id)) {
            Flash::error('That coupon code already exists.');
            redirect('/admin/coupons');
        }

        Coupon::update((int) $id, $this->payload($data, $code));
        Flash::success('Coupon updated.');
        redirect('/admin/coupons');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin('coupons');
        $this->verifyCsrf();
        Coupon::delete((int) $id);

        Flash::info('Coupon deleted.');
        redirect('/admin/coupons');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function payload(array $data, string $code): array
    {
        return [
            'code'             => $code,
            'description'      => (string) ($data['description'] ?? ''),
            'type'             => ($data['type'] ?? 'percent') === 'flat' ? 'flat' : 'percent',
            'value'            => (float) ($data['value'] ?? 0),
            'min_order_amount' => (float) ($data['min_order_amount'] ?? 0),
            'max_discount'     => ($data['max_discount'] ?? '') === '' ? null : (float) $data['max_discount'],
            'usage_limit'      => ($data['usage_limit'] ?? '') === '' ? null : (int) $data['usage_limit'],
            'starts_at'        => ($data['starts_at'] ?? '') === '' ? null : str_replace('T', ' ', (string) $data['starts_at']) . ':00',
            'expires_at'       => ($data['expires_at'] ?? '') === '' ? null : str_replace('T', ' ', (string) $data['expires_at']) . ':00',
            'is_active'        => isset($data['is_active']) ? 1 : 0,
        ];
    }
}
