<?php

declare(strict_types=1);

use App\Controllers\Admin\CategoryAdminController;
use App\Controllers\Admin\CouponAdminController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderAdminController;
use App\Controllers\Admin\ProductAdminController;
use App\Controllers\Admin\ReportAdminController;
use App\Controllers\Admin\SettingAdminController;
use App\Controllers\Admin\SupportAdminController;
use App\Controllers\Admin\UserAdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\ProfileController;
use App\Controllers\SupportController;
use App\Core\Router;

$router = new Router();

// Storefront
$router->get('/', [HomeController::class, 'index']);
$router->get('/faq', [HomeController::class, 'faq']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{slug}', [ProductController::class, 'show']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/coupon', [CartController::class, 'applyCoupon']);
$router->post('/cart/coupon/remove', [CartController::class, 'removeCoupon']);

// Checkout
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'place']);
$router->post('/checkout/confirm/{gateway}', [CheckoutController::class, 'confirm']);
$router->get('/checkout/return/{gateway}', [CheckoutController::class, 'returnFromGateway']);
$router->get('/checkout/cancel/{id}', [CheckoutController::class, 'cancel']);

// Orders and downloads
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{id}', [OrderController::class, 'show']);
$router->get('/orders/{id}/invoice', [OrderController::class, 'invoice']);
$router->get('/downloads/{token}', [OrderController::class, 'download']);

// Authentication
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/signup', [AuthController::class, 'showSignup']);
$router->post('/signup', [AuthController::class, 'signup']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/forgot', [AuthController::class, 'showForgot']);
$router->post('/forgot', [AuthController::class, 'forgot']);
$router->get('/reset/{token}', [AuthController::class, 'showReset']);
$router->post('/reset/{token}', [AuthController::class, 'reset']);

// Profile
$router->get('/profile', [ProfileController::class, 'show']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->post('/profile/password', [ProfileController::class, 'updatePassword']);

// Support
$router->get('/contact', [SupportController::class, 'contact']);
$router->post('/contact', [SupportController::class, 'submit']);
$router->get('/support', [SupportController::class, 'tickets']);
$router->get('/support/{id}', [SupportController::class, 'ticket']);
$router->post('/support/{id}/reply', [SupportController::class, 'reply']);

// Admin
$router->get('/admin', [DashboardController::class, 'index']);

$router->get('/admin/products', [ProductAdminController::class, 'index']);
$router->get('/admin/products/create', [ProductAdminController::class, 'create']);
$router->post('/admin/products', [ProductAdminController::class, 'store']);
$router->get('/admin/products/{id}/edit', [ProductAdminController::class, 'edit']);
$router->post('/admin/products/{id}', [ProductAdminController::class, 'update']);
$router->post('/admin/products/{id}/delete', [ProductAdminController::class, 'destroy']);
$router->post('/admin/products/{id}/screenshots/{screenshotId}/delete', [ProductAdminController::class, 'deleteScreenshot']);

$router->get('/admin/categories', [CategoryAdminController::class, 'index']);
$router->post('/admin/categories', [CategoryAdminController::class, 'store']);
$router->post('/admin/categories/{id}', [CategoryAdminController::class, 'update']);
$router->post('/admin/categories/{id}/delete', [CategoryAdminController::class, 'destroy']);

$router->get('/admin/orders', [OrderAdminController::class, 'index']);
$router->get('/admin/orders/{id}', [OrderAdminController::class, 'show']);
$router->post('/admin/orders/{id}/paid', [OrderAdminController::class, 'markPaid']);
$router->post('/admin/orders/{id}/refund', [OrderAdminController::class, 'refund']);

$router->get('/admin/users', [UserAdminController::class, 'index']);
$router->post('/admin/users', [UserAdminController::class, 'store']);
$router->get('/admin/users/{id}', [UserAdminController::class, 'show']);
$router->post('/admin/users/{id}/block', [UserAdminController::class, 'toggleBlock']);
$router->post('/admin/users/{id}/role', [UserAdminController::class, 'updateRole']);

$router->get('/admin/coupons', [CouponAdminController::class, 'index']);
$router->post('/admin/coupons', [CouponAdminController::class, 'store']);
$router->post('/admin/coupons/{id}', [CouponAdminController::class, 'update']);
$router->post('/admin/coupons/{id}/delete', [CouponAdminController::class, 'destroy']);

$router->get('/admin/reports', [ReportAdminController::class, 'index']);

$router->get('/admin/support', [SupportAdminController::class, 'tickets']);
$router->get('/admin/support/{id}', [SupportAdminController::class, 'ticket']);
$router->post('/admin/support/{id}/reply', [SupportAdminController::class, 'reply']);
$router->post('/admin/support/{id}/status', [SupportAdminController::class, 'setStatus']);

$router->get('/admin/faqs', [SupportAdminController::class, 'faqs']);
$router->post('/admin/faqs', [SupportAdminController::class, 'storeFaq']);
$router->post('/admin/faqs/{id}', [SupportAdminController::class, 'updateFaq']);
$router->post('/admin/faqs/{id}/delete', [SupportAdminController::class, 'destroyFaq']);

$router->get('/admin/settings', [SettingAdminController::class, 'index']);
$router->post('/admin/settings/branding', [SettingAdminController::class, 'saveBranding']);
$router->post('/admin/settings/payments', [SettingAdminController::class, 'savePayments']);
$router->post('/admin/settings/tax', [SettingAdminController::class, 'saveTax']);
$router->post('/admin/settings/notifications', [SettingAdminController::class, 'saveNotifications']);

return $router;
