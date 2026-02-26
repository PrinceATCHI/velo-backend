<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Liste toutes les catégories
    public function index()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json($categories);
    }

    // Détails d'une catégorie
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['products' => function($query) {
                $query->where('is_active', true)
                      ->with('primaryImage');
            }])
            ->firstOrFail();

        return response()->json($category);
    }
}