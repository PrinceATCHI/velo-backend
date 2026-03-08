<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images'])
            ->findOrFail($id);

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price'                  => 'required|numeric|min:0',
            'sku'                    => 'required|string|unique:products,sku',
            'stock'                  => 'required|integer|min:0',
            'category_id'            => 'required|exists:categories,id',
            'images.*'               => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'technical_specs'        => 'nullable|string',
            'sale_price'             => 'nullable|numeric|min:0',
            'is_active'              => 'boolean',
            'is_featured'            => 'boolean',
            'bike_type'              => 'nullable|string|max:50',
            'age_range'              => 'nullable|string|max:50',
            'number_of_speeds'       => 'nullable|integer',
            'wheel_size'             => 'nullable|string|max:20',
            'frame_material'         => 'nullable|string|max:50',
            'suspension_type'        => 'nullable|string|max:50',
            'special_features'       => 'nullable|string',
            'brake_style'            => 'nullable|string|max:50',
            'wheel_width'            => 'nullable|string|max:20',
            'model_name'             => 'nullable|string|max:100',
            'power_source'           => 'nullable|string|max:50',
            'wheel_material'         => 'nullable|string|max:50',
            'year'                   => 'nullable|integer',
            'battery_energy_content' => 'nullable|string|max:50',
            'battery_capacity'       => 'nullable|string|max:50',
            'max_speed'              => 'nullable|string|max:20',
            'max_range'              => 'nullable|string|max:20',
            'motor_power'            => 'nullable|string|max:50',
            'charging_time'          => 'nullable|string|max:50',
            'weight'                 => 'nullable|string|max:20',
            'max_load'               => 'nullable|string|max:20',
            'dimensions'             => 'nullable|string|max:100',
            'package_weight'         => 'nullable|string|max:20',
            'waterproof_rating'      => 'nullable|string|max:20',
            'certification'          => 'nullable|string|max:100',
            'assembly_required'      => 'boolean',
            'warranty_type'          => 'nullable|string|max:50',
            'warranty_description'   => 'nullable|string',
            'included_components'    => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        // ✅ Upload images avec couleurs
        if ($request->hasFile('images')) {
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            $colors       = $request->input('image_colors', []);

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'color'      => $colors[$index] ?? null,
                    'is_primary' => $index === $primaryIndex,
                ]);
            }
        }

        return response()->json([
            'message' => 'Produit créé avec succès',
            'product' => $product->load('images', 'category'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'price'                  => 'required|numeric|min:0',
            'sku'                    => 'required|string|unique:products,sku,' . $id,
            'stock'                  => 'required|integer|min:0',
            'category_id'            => 'required|exists:categories,id',
            'images.*'               => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'technical_specs'        => 'nullable|string',
            'sale_price'             => 'nullable|numeric|min:0',
            'is_active'              => 'boolean',
            'is_featured'            => 'boolean',
            'bike_type'              => 'nullable|string|max:50',
            'age_range'              => 'nullable|string|max:50',
            'number_of_speeds'       => 'nullable|integer',
            'wheel_size'             => 'nullable|string|max:20',
            'frame_material'         => 'nullable|string|max:50',
            'suspension_type'        => 'nullable|string|max:50',
            'special_features'       => 'nullable|string',
            'brake_style'            => 'nullable|string|max:50',
            'wheel_width'            => 'nullable|string|max:20',
            'model_name'             => 'nullable|string|max:100',
            'power_source'           => 'nullable|string|max:50',
            'wheel_material'         => 'nullable|string|max:50',
            'year'                   => 'nullable|integer',
            'battery_energy_content' => 'nullable|string|max:50',
            'battery_capacity'       => 'nullable|string|max:50',
            'max_speed'              => 'nullable|string|max:20',
            'max_range'              => 'nullable|string|max:20',
            'motor_power'            => 'nullable|string|max:50',
            'charging_time'          => 'nullable|string|max:50',
            'weight'                 => 'nullable|string|max:20',
            'max_load'               => 'nullable|string|max:20',
            'dimensions'             => 'nullable|string|max:100',
            'package_weight'         => 'nullable|string|max:20',
            'waterproof_rating'      => 'nullable|string|max:20',
            'certification'          => 'nullable|string|max:100',
            'assembly_required'      => 'boolean',
            'warranty_type'          => 'nullable|string|max:50',
            'warranty_description'   => 'nullable|string',
            'included_components'    => 'nullable|string',
        ]);

        if ($request->name !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        // ✅ Supprimer les images marquées pour suppression
        if ($request->filled('delete_image_ids')) {
            foreach ((array) $request->input('delete_image_ids') as $imgId) {
                $img = ProductImage::find($imgId);
                if ($img && $img->product_id === $product->id) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }
        }

        // ✅ Mettre à jour couleurs des images existantes
        if ($request->has('existing_image_ids')) {
            $existingIds    = (array) $request->input('existing_image_ids', []);
            $existingColors = (array) $request->input('existing_image_colors', []);

            foreach ($existingIds as $i => $imgId) {
                ProductImage::where('id', $imgId)
                    ->where('product_id', $product->id)
                    ->update(['color' => $existingColors[$i] ?? null]);
            }
        }

        // ✅ Définir image principale existante
        if ($request->filled('primary_existing_image_id')) {
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);
            ProductImage::where('id', $request->input('primary_existing_image_id'))
                ->where('product_id', $product->id)
                ->update(['is_primary' => true]);
        }

        // ✅ Upload nouvelles images avec couleurs
        if ($request->hasFile('images')) {
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            $colors       = $request->input('image_colors', []);

            // Si une nouvelle image principale est définie, retirer l'ancienne
            if ($request->has('primary_image_index')) {
                ProductImage::where('product_id', $product->id)
                    ->update(['is_primary' => false]);
            }

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'color'      => $colors[$index] ?? null,
                    'is_primary' => $index === $primaryIndex,
                ]);
            }
        }

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'product' => $product->load('images', 'category'),
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();

        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

    public function deleteImage($imageId)
    {
        $image   = ProductImage::findOrFail($imageId);
        $product = Product::with('images')->findOrFail($image->product_id);

        if ($product->images->count() <= 1) {
            return response()->json(['message' => 'Impossible de supprimer la dernière image'], 400);
        }

        Storage::disk('public')->delete($image->image_path);

        if ($image->is_primary) {
            $newPrimary = $product->images->where('id', '!=', $image->id)->first();
            if ($newPrimary) $newPrimary->update(['is_primary' => true]);
        }

        $image->delete();

        return response()->json(['message' => 'Image supprimée avec succès']);
    }

    public function setPrimaryImage(Request $request, $productId)
    {
        $request->validate(['image_id' => 'required|exists:product_images,id']);

        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        ProductImage::where('id', $request->image_id)
            ->where('product_id', $productId)
            ->update(['is_primary' => true]);

        return response()->json(['message' => 'Image principale mise à jour']);
    }
}