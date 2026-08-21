<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Cart;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\Coupon;
use App\Models\Product;

final class CartController extends Controller
{
    public function index(): void
    {
        $this->render('pages/cart', ['pageTitle' => 'Your cart'] + ['totals' => Cart::totals()]);
    }

    public function add(): void
    {
        $this->verifyCsrf();
        $productId = (int) $this->request->input('product_id', 0);
        $product = Product::find($productId);

        if ($product === null || $product['status'] !== 'active') {
            Flash::error('That product is not available.');
            $this->back('/products');
        }

        if (Cart::has($productId)) {
            Flash::info('That product is already in your cart.');
        } else {
            Cart::add($productId);
            Flash::success(e((string) $product['title']) . ' added to your cart.');
        }

        redirect($this->request->input('buy_now') ? '/checkout' : '/cart');
    }

    public function remove(): void
    {
        $this->verifyCsrf();
        Cart::remove((int) $this->request->input('product_id', 0));
        Flash::info('Item removed from your cart.');
        redirect('/cart');
    }

    public function applyCoupon(): void
    {
        $this->verifyCsrf();
        $code = strtoupper((string) $this->request->input('code', ''));
        $coupon = Coupon::findValid($code);

        if ($coupon === null) {
            Flash::error('That coupon code is invalid or has expired.');
            $this->back('/cart');
        }

        $subtotal = Cart::totals()['subtotal'];
        $reason = Coupon::reason($coupon, $subtotal);
        if ($reason !== null) {
            Flash::error($reason);
            $this->back('/cart');
        }

        Cart::applyCoupon($code);
        Flash::success('Coupon ' . e($code) . ' applied.');
        $this->back('/cart');
    }

    public function removeCoupon(): void
    {
        $this->verifyCsrf();
        Cart::removeCoupon();
        Flash::info('Coupon removed.');
        $this->back('/cart');
    }
}
