<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Testimonial;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('pages/home', [
            'pageTitle'    => 'Digital products that ship instantly',
            'featured'     => Product::featured(6),
            'categories'   => Category::active(),
            'testimonials' => Testimonial::active(3),
            'faqs'         => array_slice(Faq::active(), 0, 4),
        ]);
    }

    public function faq(): void
    {
        $this->render('pages/faq', ['pageTitle' => 'Frequently asked questions', 'faqs' => Faq::active()]);
    }
}
