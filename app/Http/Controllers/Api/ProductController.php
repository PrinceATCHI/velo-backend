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
        $query = Product::with(['category', 'primaryImage', 'reviews'])
            ->where('is_active', true);

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtre par prix
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Recherche
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'popularity') {
            $query->orderBy('views', 'desc');
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $products = $query->paginate(12);

        return response()->json($products);
    }

    // Produits en vedette
    public function featured()
    {
        $products = Product::with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
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
                'variants',
                'reviews' => function($query) {
                    $query->where('is_approved', true)
                          ->with('user')
                          ->latest();
                }
            ])
            ->firstOrFail();

        // Incrémenter les vues
        $product->increment('views');

        return response()->json($product);
    }

    // Produits similaires
    public function related($id)
    {
        $product = Product::findOrFail($id);
        
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->with('primaryImage')
            ->limit(4)
            ->get();

        return response()->json($related);
    }
}

