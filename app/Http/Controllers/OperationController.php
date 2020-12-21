<?php

namespace App\Http\Controllers;

use App\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $operations = Operation::get()
            ->groupBy(function ($op) {
                return Carbon::parse($op->created_at)->format('M');
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
}
