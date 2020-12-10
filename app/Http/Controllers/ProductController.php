<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $validationRules = [
        'designation' => 'required|string',
        'prix_de_vente' => 'required|numeric|min:0',
        'stock_initial' => 'required|integer|min:0',
        'stock_actuel' => 'required|numeric|min:0',
        'prix_dachat' => 'required|integer|min:0',
        'montant' => 'required|numeric|min:0',
        'image' => 'image|mimes:jpeg,png,jpg|max:2048'
    ];

    // return all products
    public function index()
    {
        $products = Product::all();

        return response()->json(['products' => $products], 200);
    }

    // return the product with the given id
    public function read(Request $request)
    {
        $this->validate($request, ['productId' => 'required|numeric']);

        $productId = $request->input('productId');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
        }

        return response()->json(['product' => $product], 200);
    }

    public function create(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $product = Product::create([
            'designation' => $request->input('designation'),
            'prix_de_vente' => $request->input('prix_de_vente'),
            'stock_initial' => $request->input('stock_initial'),
            'stock_actuel' => $request->input('stock_actuel'),
            'prix_de_dachat' => $request->input('prix_dachat'),
            'montant' => $request->input('montant'),
            'image' => ''
        ]);

        $imageName = 'product_' . $product->id . '.' . request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images'), $imageName);
        $product->image = env('IMAGES_DIRECTORY') . '/' . $imageName;

        $product->save();

        return response()->json(['message' => 'CREATED', 'product' => $product], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, ['productId' => 'required|numeric']);

        $productId = $request->input('productId');
        $product = Product::find($productId);

        if ($product) {
            $product->designation = $request->input('designation') != null ? $request->input('designation') : $product->designation;
            $product->prix_de_vente = $request->input('prix_de_vente') != null ? $request->input('prix_de_vente') : $product->prix_de_vente;
            $product->stock_initial = $request->input('stock_initial') != null ? $request->input('stock_initial') : $product->stock_initial;
            $product->stock_actuel = $request->input('stock_actuel') != null ? $request->input('stock_actuel') : $product->stock_actuel;
            $product->prix_de_dachat = $request->input('prix_de_dachat') != null ? $request->input('prix_de_dachat') : $product->prix_de_dachat;
            $product->montant = $request->input('montant') != null ? $request->input('montant') : $product->montant;

            // replace the product image in /images directory
            if ($request->image) {
                $imageSrc = $product->image;
                $image = $request->image;
                $imageName = str_replace(env('IMAGES_DIRECTORY'), '', $imageSrc);
                $image->move(public_path('images'), $imageName);
            }

            $product->save();

            return response()->json(['message' => 'UPDATED', 'product' => $product], 200);
        }

        return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
    }

    public function delete(Request $request)
    {
        $this->validate($request, ['productId' => 'required|numeric']);

        $productId = $request->input('productId');
        $product = Product::find($productId);

        if ($product) {
            $imagePath = str_replace(env('IMAGES_DIRECTORY'), public_path('images'), $product->image);
            unlink($imagePath);

            $product->delete();

            return response()->json(['message' => 'PRODUCT DELETED!'], 200);
        }

        return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
    }
}