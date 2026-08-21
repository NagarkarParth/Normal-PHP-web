<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

final class ProductController extends Controller
{
    private const PER_PAGE = 9;

    public function index(): void
    {
        $page = max(1, (int) $this->request->query('page', 1));
        $filters = [
            'q'         => (string) $this->request->query('q', ''),
            'category'  => (string) $this->request->query('category', ''),
            'min_price' => (string) $this->request->query('min_price', ''),
            'max_price' => (string) $this->request->query('max_price', ''),
            'sort'      => (string) $this->request->query('sort', 'newest'),
        ];

        $result = Product::search($filters, self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        $this->render('pages/products', [
            'pageTitle'  => 'All products',
            'products'   => $result['items'],
            'total'      => $result['total'],
            'page'       => $page,
            'pages'      => (int) ceil($result['total'] / self::PER_PAGE),
            'filters'    => $filters,
            'categories' => Category::active(),
        ]);
    }

    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if ($product === null || $product['status'] !== 'active') {
            http_response_code(404);
            $this->render('errors/404', ['pageTitle' => 'Product not found']);

            return;
        }

        Product::incrementViews((int) $product['id']);

        $this->render('pages/product', [
            'pageTitle'   => (string) $product['title'],
            'product'     => $product,
            'screenshots' => Product::screenshots((int) $product['id']),
            'related'     => Product::related((int) $product['id'], $product['category_id'] === null ? null : (int) $product['category_id']),
        ]);
    }
}
