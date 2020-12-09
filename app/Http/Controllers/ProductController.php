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
    public function read(Product $product)
    {
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

        $imageName = $product->designation . '_' . $product->id . '.' . request()->image->getClientOriginalExtension();
        request()->image->move(public_path('images'), $imageName);
        $product->image = env('APP_URL'). '/images/' . $imageName;

        $product->save();

        return response()->json(['message' => 'CREATED', 'product' => $product], 200);
    }
}
