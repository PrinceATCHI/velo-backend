<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Liste des produits avec filtres
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Filtre par catégorie (slug)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filtre en stock
        if ($request->in_stock === 'true') {
            $query->where('stock', '>', 0);
        }

        // Filtre en promotion
        if ($request->on_sale === 'true') {
            $query->whereNotNull('sale_price');
        }

        // Filtre nouveautés
        if ($request->is_new === 'true') {
            $query->where('is_new', true);
        }

        // Recherche (FR + DE)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_de', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Tri
        match ($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderBy('is_featured', 'desc'),
            'rating'     => $query->orderBy('id', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(20);

        return response()->json([
            'data'  => $products->items(),
            'meta'  => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    // Produits en vedette
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 8);

        $products = Product::with(['category', 'images'])
            ->where('is_featured', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($products);
    }

    // Détails d'un produit
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with([
                'category',
                'images',
                'reviews' => function ($query) {
                    $query->with('user')->latest();
                },
            ])
            ->firstOrFail();

        return response()->json($product);
    }

    // Produits similaires
    public function related($id)
    {
        $product = Product::findOrFail($id);

        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('stock', '>', 0)
            ->with('images')
            ->limit(4)
            ->get();

        return response()->json($related);
    }
}