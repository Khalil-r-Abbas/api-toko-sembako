<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductPage extends BaseController
{
    // GET /produk
    public function index()
    {
        $products = (new ProductModel())
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('products/index', ['products' => $products]);
    }
}
