<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Models\Category;

final class CategoryAdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin('products');

        $this->render('admin/categories', [
            'pageTitle'  => 'Categories',
            'categories' => Category::all(),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        $name = (string) $this->request->input('name', '');

        if (trim($name) === '') {
            Flash::error('Category name is required.');
            redirect('/admin/categories');
        }

        Category::create($name, (string) $this->request->input('description', ''), $this->request->has('is_active'));
        Flash::success('Category added.');
        redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();

        Category::update(
            (int) $id,
            (string) $this->request->input('name', ''),
            (string) $this->request->input('description', ''),
            $this->request->has('is_active')
        );

        Flash::success('Category updated.');
        redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin('products');
        $this->verifyCsrf();
        Category::delete((int) $id);

        Flash::info('Category deleted.');
        redirect('/admin/categories');
    }
}
