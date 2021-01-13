<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Notification as NotificationResource;
use App\Operation;

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
        $products = DB::table('products')
            ->orderBy('trending', 'desc')
            ->get(); // TODO: ->paginate(15);

        /*
        foreach ($products as $product) {
            $gain = ($product->stock_initial - $product->stock_actuel) * ($product->prix_de_vente - $product->prix_de_dachat);
            $product->gain = $gain;
        }
        */

        return response()->json(['products' => $products], 200);
    }

    // return the product with the given id
    public function read(Request $request)
    {
        $this->validate($request, ['productId' => 'required|numeric']);

        $productId = $request->input('productId');
        $product = $this->findProductByID($productId);

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
            'stock_min' => $request->input('stock_min'),
            'prix_de_dachat' => $request->input('prix_dachat'),
            'montant' => $request->input('montant'),
            'image' => ''
        ]);

        if ($request->image) {
            $imageName = 'product_' . $product->id . '.' . request()->image->getClientOriginalExtension();
            request()->image->move(public_path('images'), $imageName);
            $product->image = env('IMAGES_DIRECTORY') . '/' . $imageName;
        }

        $product->save();

        return response()->json(['message' => 'CREATED', 'product' => $product], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|numeric']);

        $productId = $request->input('id');
        $product = Product::find($productId);

        if ($product) {
            $product->designation = $request->input('designation') != null ? $request->input('designation') : $product->designation;
            $product->prix_de_vente = $request->input('prix_de_vente') != null ? $request->input('prix_de_vente') : $product->prix_de_vente;
            $product->stock_initial = $request->input('stock_initial') != null ? $request->input('stock_initial') : $product->stock_initial;
            $product->stock_actuel = $request->input('stock_actuel') != null ? $request->input('stock_actuel') : $product->stock_actuel;
            $product->stock_min = $request->input('stock_min') != null ? $request->input('stock_min') : $product->stock_min;
            $product->prix_de_dachat = $request->input('prix_de_dachat') != null ? $request->input('prix_de_dachat') : $product->prix_de_dachat;
            $product->montant = $request->input('montant') != null ? $request->input('montant') : $product->montant;

            // replace the product image in /images directory
            // if ($request->image) {
            //     $imageSrc = $product->image;
            //     $image = $request->image;
            //     $imageName = str_replace(env('IMAGES_DIRECTORY'), '', $imageSrc);
            //     $image->move(public_path('images'), $imageName);
            // }

            $product->save();

            return response()->json(['message' => 'UPDATED', 'product' => $product], 200);
        }

        return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
    }

    public function delete(Request $request)
    {
        $this->validate($request, ['id' => 'required|numeric']);

        $productId = $request->input('id');

        if ($this->deleteProduct($productId)) {
            return response()->json(['message' => 'PRODUCT DELETED!'], 200);
        }

        return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
    }

    public function multipleDelete(Request $request)
    {
        $this->validate($request, ['products' => 'required']);

        $products = $request->input('products');
        $deleted = true;
        $output = array();

        foreach ($products as $product) {
            if (!$this->deleteProduct($product)) {
                $deleted = false;
                array_push($output, $product);
            }
        }

        if ($deleted) {
            return response()->json(['message' => 'PRODUCTS DELETED!'], 200);
        }

        return response()->json([
            'message' => 'SOME PRODUCTS WERE NOT FOUND!',
            'products not found' => $output
        ], 400);
    }

    public function buyProduct(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
        ]);

        $productId = $request->input('productId');
        $quantity = $request->input('quantity');

        $product = $this->findProductByID($productId);

        if (!$product) {
            return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
        }

        if ($product->stock_actuel < $quantity) {
            return response()->json(['message' => 'QUANITY IN STOCK IS LESS THAN THE ONE REQUESTED!'], 400);
        }
        $this->modifyProductQuantity($product, $quantity, "buy");

        return response()->json(['message' => 'OPERARTION DONE!'], 200);
    }

    public function sellProduct(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
        ]);

        $productId = $request->input('productId');
        $quantity = $request->input('quantity');

        $product = $this->findProductByID($productId);

        if (!$product) {
            return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
        }

        if ($product->stock_actuel < $quantity) {
            return response()->json(['message' => 'QUANITY IN STOCK IS LESS THAN THE ONE REQUESTED!'], 400);
        }
        $this->modifyProductQuantity($product, $quantity, "sell");

        return response()->json(['message' => 'OPERARTION DONE!'], 200);
    }

    public function readOperations(Request $request)
    {
        // $this->validate($request, ['productId' => 'requkired|numeric']);

        $product = $this->findProductByID(1);

        if (!$product) {
            return response()->json(['message' => 'PRODUCT NOT FOUND!'], 400);
        }

        $operations = $product->operations
            ->groupBy(function ($op) {
                return Carbon::parse($op->created_at)->format('Y-M');
            });

        $response = collect();
        foreach ($operations as $month => $ops) {
            $monthOps = collect(['operations' => $ops]);

            $sum = 0;
            $quantity = 0;
            foreach ($ops as $op) {
                if ($op->type == 'sell') {
                    $sum += $op->montant;
                    $quantity += $op->quantity;
                } else {
                    $sum -= $op->montant;
                }
            }

            $monthOps->put('sum', $sum);
            $monthOps->put('selledQuantity', $quantity);

            $response->put($month, $monthOps);
        }

        return response()->json(['response' => $response], 200);
    }

    public function trendingProducts()
    {
        $operations = Operation::groupBy('product_id')
            ->selectRaw('product_id , products.designation,  sum(quantity) as sellQuantity')
            ->join('products', 'products.id', '=', 'operations.product_id')
            ->orderBy('sellQuantity', 'desc')
            ->having('sellQuantity', '>', 0)
            ->take(5)
            ->get();

        // dd($operations);
        return response()->json($operations, 200);
    }

    public function lowStockProducts()
    {
        $products = Product::whereRaw("stock_min  >  stock_actuel")->get();
        return response()->json(NotificationResource::collection($products), 200);
    }

    private function modifyProductQuantity($product, $quantity, $operation)
    {
        $montant = 0;
        if ($operation == "buy") {
            $product->stock_actuel += $quantity;
            $montant = $quantity * $product->prix_de_dachat;
        } else if ($operation == "sell") {
            $product->stock_actuel -= $quantity;
            $product->trending++; // increment trending attribute
            $montant = $quantity * $product->prix_de_vente;
        }

        $product->operations()->create([
            'type' => $operation,
            'montant' => $montant,
            'quantity' => $quantity
        ]);

        $product->save();
    }

    private function findProductByID($id)
    {
        $product = Product::find($id);

        $gain = ($product->stock_initial - $product->stock_actuel) * ($product->prix_de_vente - $product->prix_de_dachat);
        $product->gain = $gain;

        // $product->load('operations');

        return $product ? $product : null;
    }

    private function deleteProduct($id)
    {
        $product = $this->findProductByID($id);

        if ($product) {
            $imagePath = str_replace(env('IMAGES_DIRECTORY'), public_path('images'), $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $product->delete();

            return true;
        }

        return false;
    }
}
