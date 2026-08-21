-- YBT Digital - demo data
-- Usage: mysql -u root -p ybt_digital < database/seed.sql
-- Default logins: admin@ybtdigital.test / Admin@123 and editor@ybtdigital.test / Editor@123
--                 customer demo@ybtdigital.test / Demo@123

USE `ybt_digital`;

INSERT INTO users (name, email, password_hash, role) VALUES
    ('Super Admin', 'admin@ybtdigital.test', '$2y$12$.GHF7ZzKJRbyDmdp5Kg.WufCwZZ6MEV3ZJ2oELjswlTwow7NFM6KW', 'super_admin'),
    ('Content Editor', 'editor@ybtdigital.test', '$2y$12$qPhr5MKYavk1KfP9CM85WOKptu05XWFimh5ZfZNtdcZB9CIT8.6eS', 'editor'),
    ('Demo Customer', 'demo@ybtdigital.test', '$2y$12$1BPurVmKALd.FiMPkSzZZuZ89NDMbV/688tewUzkW6jYz5QAMl1a6', 'customer')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO categories (name, slug, description) VALUES
    ('UI Kits', 'ui-kits', 'Design systems and component libraries'),
    ('Templates', 'templates', 'Website and dashboard templates'),
    ('eBooks', 'ebooks', 'Guides and playbooks for builders'),
    ('Scripts', 'scripts', 'Ready to deploy PHP and JS scripts')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO products (category_id, title, slug, short_description, description, price, compare_at_price, status, is_featured, sales_count, file_name) VALUES
    (1, 'Nova UI Kit', 'nova-ui-kit', '180+ responsive components for modern SaaS dashboards.', 'Nova UI Kit ships 180+ handcrafted components, dark and light themes, and Figma sources so your team can move from idea to production in hours.', 2499.00, 3999.00, 'active', 1, 42, 'nova-ui-kit.zip'),
    (2, 'Commerce Starter Template', 'commerce-starter-template', 'Production ready storefront template with checkout flows.', 'A complete storefront template: landing page, catalogue, cart, checkout and a customer dashboard. Fully responsive and ready to brand.', 1899.00, 2999.00, 'active', 1, 30, 'commerce-starter.zip'),
    (3, 'Digital Product Playbook', 'digital-product-playbook', 'Launch, price and scale digital products profitably.', 'A 140 page playbook covering positioning, pricing ladders, launch sequences and the analytics that actually matter for digital products.', 799.00, 1299.00, 'active', 1, 88, 'digital-product-playbook.pdf'),
    (4, 'Invoice Generator Script', 'invoice-generator-script', 'PHP + MySQL invoicing script with GST support.', 'Generate branded GST ready invoices, track payments and export PDFs. Includes install script and one year of updates.', 1299.00, NULL, 'active', 0, 17, 'invoice-generator.zip'),
    (1, 'Aurora Icon Pack', 'aurora-icon-pack', '1200 pixel perfect icons in SVG and font formats.', 'Aurora ships 1200 icons in outline, solid and duotone styles, delivered as SVG, icon font and React components.', 599.00, 999.00, 'active', 0, 65, 'aurora-icons.zip'),
    (2, 'Analytics Dashboard Kit', 'analytics-dashboard-kit', 'Charts, tables and layouts for data heavy apps.', 'Everything you need to ship an analytics product: 20 chart layouts, filters, data tables and export-ready report views.', 2199.00, NULL, 'active', 0, 12, 'analytics-dashboard.zip')
ON DUPLICATE KEY UPDATE short_description = VALUES(short_description);

INSERT INTO coupons (code, description, type, value, min_order_amount, max_discount, usage_limit, expires_at, is_active) VALUES
    ('WELCOME10', '10% off your first order', 'percent', 10.00, 0.00, 500.00, 500, DATE_ADD(NOW(), INTERVAL 90 DAY), 1),
    ('FLAT200', 'Flat 200 off orders above 1500', 'flat', 200.00, 1500.00, NULL, 100, DATE_ADD(NOW(), INTERVAL 30 DAY), 1)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO faqs (question, answer, sort_order) VALUES
    ('How do I receive my product after payment?', 'Your download unlocks instantly on the Orders page and a copy of the receipt is emailed to you.', 1),
    ('Which payment methods are supported?', 'Cards, UPI, netbanking and wallets through Razorpay, Stripe or PayPal depending on the gateway your store admin has enabled.', 2),
    ('Can I re-download a purchase later?', 'Yes. Every purchase stays in your account. If the store enables download expiry, the remaining window is shown on the order.', 3),
    ('Do you offer refunds?', 'Digital goods are refundable within 7 days if the product is broken and support cannot fix it.', 4)
ON DUPLICATE KEY UPDATE answer = VALUES(answer);

INSERT INTO testimonials (name, role, message, rating, sort_order) VALUES
    ('Aarav Mehta', 'Founder, Studio Nine', 'We shipped our client dashboard a full month early thanks to the Nova UI Kit. Worth every rupee.', 5, 1),
    ('Sara Iqbal', 'Product Designer', 'Clean files, sensible naming and dark mode out of the box. This is how digital products should be sold.', 5, 2),
    ('Rahul Verma', 'Indie Developer', 'Checkout took thirty seconds and the download was instant. The invoice was already in my inbox.', 5, 3)
ON DUPLICATE KEY UPDATE message = VALUES(message);

INSERT INTO settings (setting_key, setting_value) VALUES
    ('site_name', 'YBT Digital'),
    ('site_tagline', 'Premium digital products, delivered instantly.'),
    ('footer_text', 'Built for creators shipping digital products.'),
    ('support_email', 'support@ybtdigital.test'),
    ('currency', 'INR'),
    ('tax_label', 'GST'),
    ('tax_rate', '18'),
    ('payment_gateway', 'offline'),
    ('razorpay_key_id', ''),
    ('razorpay_key_secret', ''),
    ('stripe_publishable_key', ''),
    ('stripe_secret_key', ''),
    ('paypal_client_id', ''),
    ('paypal_secret', ''),
    ('paypal_mode', 'sandbox'),
    ('download_expiry_days', '0'),
    ('notify_order_paid', '1'),
    ('notify_order_failed', '1'),
    ('notify_welcome', '1'),
    ('notify_ticket_reply', '1')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
