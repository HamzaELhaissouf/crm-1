<?php

namespace App\Http\Controllers;

use App\Client;
use App\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Operation as OperationResource;
use App\Product;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $operations = Operation::get()
            ->groupBy(function ($op) {
                return Carbon::parse($op->created_at)->format('y-M');
            });

        $response = collect();
        foreach ($operations as $month => $ops) {
            $monthOps = collect(['operations' => $ops]);

            $sum = 0;
            foreach ($ops as $op) {
                if ($op->type == 'sell') {
                    $sum += $op->montant;
                } else {
                    $sum -= $op->montant;
                }
            }

            $monthOps->put('sum', $sum);

            $response->put($month, $monthOps);
        }

        return response()->json(['response' => $response], 200);
    }

    public function opResource()
    {
        return response()->json(['data' => OperationResource::collection(Operation::all())], 200);
    }

    public function read(Request $request)
    {
        $this->validate($request, ['operationId' => 'required|numeric']);

        $operation = $this->findOperationById($request->input('operationId'));

        if (!$operation) {
            return response()->json(['OPERATION NOT FOUND!'], 400);
        }

        return response()->json(['operation' => $operation], 200);
    }

    private function findOperationById($id)
    {
        $operation = Operation::find($id);

        return $operation ? $operation : null;
    }

    public function cards()
    {
        // dd(Product::sum('stock_actuel'));

        $products = Product::count();
        $unites  = Product::sum('stock_actuel');
        $montant = DB::select('select sum(`prix_de_vente` * `stock_actuel`) as montant from products');
        $sells = Operation::whereMonth('created_at', Carbon::now()->month)->where('type', 'sell')->sum('prix_achat') * Operation::whereMonth('created_at', Carbon::now()->month)->where('type', 'sell')->sum('quantity');
        $buys = Operation::whereMonth('created_at', Carbon::now()->month)->where('type', 'sell')->sum('prix_achat') * Operation::whereMonth('created_at', Carbon::now()->month)->where('type', 'buy')->sum('quantity');
        $clients = Client::count();

// dd($montant);

        return response()->json(['cards' => [
            ['data' => $products, 'title' => 'Total produits', 'color' => 'red lighten-1', 'icon' => 'fas fa-cubes'],
            ['data' => $unites, 'title' => 'Total unites', 'color' => 'green lighten-1', 'icon' => 'fas fa-drum-steelpan'],
            ['data' => $montant[0]->montant, 'currency' => 'DH', 'title' => 'Montant total', 'color' => 'blue lighten-1', 'icon' => 'fas fa-euro-sign'],
            ['data' => $sells, 'title' => 'Total Achat', 'color' => 'red lighten-2', 'icon' => 'fas fa-shopping-cart'],
            ['data' => $buys, 'title' => 'Total vente', 'color' => 'green lighten-2', 'icon' => 'fas fa-credit-card'],
            ['data' => $clients,  'title' => 'Effictive clients', 'color' => 'blue lighten-2', 'icon' => 'fas fa-users'],
        ]], 200);
    }


    public function operationByMonth(Request $request)
    {
        $this->validate($request, ['id' => 'required|numeric', 'month' => 'required']);

        $id = $request->input('id');
        $month = $request->input('month');

        $operations   =  Operation::whereMonth('created_at', $month)->where('product_id', $id)->get();
        return response()->json(['data' => OperationResource::collection($operations)], 200);
    }
    /*
     public function operationByMonth(Request $request)
    {
         $this->validate($request, ['id' => 'required|numeric' , 'month'=>'required']);

        $id = 1; //$request->input('id');
        $month = 1;//$request->input('month');
      
        $operations =  Operation::whereMonth('created_at' ,$month )->where('product_id' , $id )->get();
        $dataArray = [];
        foreach ($operations as $operation) {
            $dataArray[]= $operation["created_at"].": ". $operation["montant"];
            // $dataArray[] = $operation["created_at"].",". $operation["montant"];
        }
        // $n = [];
        // foreach($dataArray as $arr) {
        //     $arr1 = explode(': ', $arr);
        //     $key = $arr1[0];
        //     $value = $arr1[1];
        //     $n/ = [$key => $value];
        // }
        // dd($n);
//dd(OperationResource::collection($newOperations)->collapse());
        return response()->json(['data' => $dataArray], 200);
    } 
    */
}
