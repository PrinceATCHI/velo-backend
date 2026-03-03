<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Liste toutes les catégories actives
    public function index()
    {
        $categories = Category::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    // Détails d'une catégorie + ses produits
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->where('stock', '>', 0)
                      ->with('images');
            }])
            ->firstOrFail();

        return response()->json($category);
    }
}