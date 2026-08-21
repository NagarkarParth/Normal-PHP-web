# ModzLab

Digital product storefront and admin dashboard built with plain PHP 8.2+, MySQL/MariaDB and MDB UI Kit.
Mobile-first responsive UI (app-style bottom navigation on phones, professional desktop layout), dark/light mode,
configurable payment gateways (Razorpay / Stripe / PayPal / offline), coupons, taxes, secure downloads and invoices.

## Requirements

- PHP 8.2 or newer with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`
- MySQL 8 or MariaDB 10.6+

## Setup

```bash
cp config/config.example.php config/config.php   # then edit credentials (file is git-ignored)

mysql -u root -p < database/schema.sql
mysql -u root -p ybt_digital < database/seed.sql

php -S localhost:8000 -t public router.php
```

The app is then available at http://localhost:8000 and the dashboard at http://localhost:8000/admin.

Configuration can also come from environment variables (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`,
`APP_URL`, `APP_DEBUG`, ...). `config/config.php` overrides `config/config.example.php` when present.

## Demo accounts (seed data, development only)

| Role        | Email                     | Password    |
|-------------|---------------------------|-------------|
| Super admin | admin@ybtdigital.test     | Admin@123   |
| Editor      | editor@ybtdigital.test    | Editor@123  |
| Customer    | demo@ybtdigital.test      | Demo@123    |

Change these before deploying anywhere public.

## Layout

```
app/
  Controllers/        storefront + Admin/ controllers
  Core/               router, request, view, auth, cart, settings, mailer, uploader, helpers
  Models/             thin PDO-backed data models
  Payments/           gateway abstraction (razorpay, stripe, paypal, offline)
  Views/              layouts, partials, pages, auth, admin, errors
config/               config.example.php (copy to config.php)
database/             schema.sql, seed.sql
public/               web root: index.php + assets
storage/              logs, uploads and product files (products/ is never web accessible)
```

## Payments

Gateway keys are entered in **Admin → Settings → Payments** and stored in the `settings` table.
When no keys are configured the storefront falls back to the offline gateway, which records orders as
pending so an admin can mark them paid manually — that keeps the full purchase flow testable locally.

## Secure downloads

Product files are written to `storage/products/`, outside the web root. Each order item gets a random
download token; every hit re-checks ownership, payment status and the optional expiry window
(**Admin → Settings → Tax & downloads**) before streaming the file, and each download is logged.
