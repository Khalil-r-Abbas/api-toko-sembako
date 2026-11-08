<?php

namespace App\Controllers;

use App\Libraries\RecipeService;
use App\Models\ProductModel;

class BikinMakan extends BaseController
{
    // GET /bikin-makan
    public function index()
    {
        $products = (new ProductModel())
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('recipes/index', ['products' => $products]);
    }

    // GET /bikin-makan/cari?names[]=Telur&names[]=Nasi
    public function cari()
    {
        $names = $this->request->getGet('names');
        $names = is_array($names) ? $names : [];

        $names = array_values(array_unique(array_filter(
            array_map(static fn ($n) => is_string($n) ? trim($n) : '', $names)
        )));
        $names = array_slice($names, 0, 30);

        $data = (new RecipeService())->findByProducts($names);

        return $this->response->setJSON([
            'status' => 200,
            'total'  => count($data),
            'data'   => $data,
        ]);
    }
}
