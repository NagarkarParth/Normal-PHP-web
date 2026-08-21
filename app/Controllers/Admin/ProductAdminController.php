<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Product;

final class ProductAdminController extends Controller
{
    private const PER_PAGE = 15;

    public function index(): void
    {
        $this->requireAdmin('products');
        $page = max(1, (int) $this->request->query('page', 1));
        $search = (string) $this->request->query('q', '');
        $status = (string) $this->request->query('status', '');

        $total = Product::adminCount($search, $status);

        $this->render('admin/products/index', [
            'pageTitle' => 'Products',
            'products'  => Product::adminList($search, $status, self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'total'     => $total,
            'page'      => $page,
            'pages'     => (int) ceil($total / self::PER_PAGE),
            'search'    => $search,
            'status'    => $status,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin('products');

        $this->render('admin/products/form', [
            'pageTitle'   => 'New product',
            'product'     => null,
            'categories'  => Category::all(),
            'screenshots' => [],
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        $data = $this->request->all();

        $validator = Validator::make($data)
            ->required('title', 'Title')
            ->required('price', 'Price')->numeric('price', 'Price');

        if ($validator->fails()) {
            $this->withInput($data);
            Flash::error((string) $validator->firstError());
            redirect('/admin/products/create');
        }

        $payload = $this->payload($data);
        $payload['slug'] = Product::uniqueSlug((string) $data['title']);

        $file = $this->request->file('product_file');
        if ($file !== null) {
            $stored = Uploader::productFile($file);
            if ($stored !== null) {
                $payload['file_path'] = $stored['path'];
                $payload['file_name'] = $stored['name'];
                $payload['file_size'] = $stored['size'];
            }
        }

        $thumbnail = $this->request->file('thumbnail');
        if ($thumbnail !== null) {
            $payload['thumbnail'] = Uploader::image($thumbnail);
        }

        $productId = Product::create($payload);
        $this->storeScreenshots($productId);

        Flash::success('Product created.');
        redirect('/admin/products/' . $productId . '/edit');
    }

    public function edit(string $id): void
    {
        $this->requireAdmin('products');
        $product = Product::find((int) $id);

        if ($product === null) {
            Flash::error('Product not found.');
            redirect('/admin/products');
        }

        $this->render('admin/products/form', [
            'pageTitle'   => 'Edit ' . $product['title'],
            'product'     => $product,
            'categories'  => Category::all(),
            'screenshots' => Product::screenshots((int) $product['id']),
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        $product = Product::find((int) $id);

        if ($product === null) {
            Flash::error('Product not found.');
            redirect('/admin/products');
        }

        $data = $this->request->all();
        $validator = Validator::make($data)
            ->required('title', 'Title')
            ->required('price', 'Price')->numeric('price', 'Price');

        if ($validator->fails()) {
            Flash::error((string) $validator->firstError());
            redirect('/admin/products/' . $id . '/edit');
        }

        $payload = $this->payload($data);
        $payload['slug'] = Product::uniqueSlug((string) $data['title'], (int) $id);

        $file = $this->request->file('product_file');
        if ($file !== null) {
            $stored = Uploader::productFile($file);
            if ($stored !== null) {
                $payload['file_path'] = $stored['path'];
                $payload['file_name'] = $stored['name'];
                $payload['file_size'] = $stored['size'];
            }
        }

        $thumbnail = $this->request->file('thumbnail');
        if ($thumbnail !== null) {
            $uploaded = Uploader::image($thumbnail);
            if ($uploaded !== null) {
                $payload['thumbnail'] = $uploaded;
            }
        }

        Product::update((int) $id, $payload);
        $this->storeScreenshots((int) $id);

        Flash::success('Product updated.');
        redirect('/admin/products/' . $id . '/edit');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        Product::delete((int) $id);

        Flash::success('Product deleted.');
        redirect('/admin/products');
    }

    public function deleteScreenshot(string $id, string $screenshotId): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        Product::deleteScreenshot((int) $screenshotId, (int) $id);

        Flash::info('Screenshot removed.');
        redirect('/admin/products/' . $id . '/edit');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'category_id'       => ($data['category_id'] ?? '') === '' ? null : (int) $data['category_id'],
            'title'             => (string) $data['title'],
            'short_description' => (string) ($data['short_description'] ?? ''),
            'description'       => (string) ($data['description'] ?? ''),
            'price'             => (float) $data['price'],
            'compare_at_price'  => ($data['compare_at_price'] ?? '') === '' ? null : (float) $data['compare_at_price'],
            'demo_url'          => (string) ($data['demo_url'] ?? ''),
            'status'            => ($data['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
            'is_featured'       => isset($data['is_featured']) ? 1 : 0,
        ];
    }

    private function storeScreenshots(int $productId): void
    {
        $files = $_FILES['screenshots'] ?? null;
        if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
            return;
        }

        foreach (array_keys($files['name']) as $index) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }
            $path = Uploader::image([
                'name'     => $files['name'][$index],
                'type'     => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'size'     => $files['size'][$index],
            ], 'screenshots');

            if ($path !== null) {
                Product::addScreenshot($productId, $path);
            }
        }
    }
}
