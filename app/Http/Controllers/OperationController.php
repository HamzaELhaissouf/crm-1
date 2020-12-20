<?php

namespace App\Http\Controllers;

use App\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function index()
    {
        // $operations = Operation::all();
        $operations = Operation::get()
                                ->groupBy(function ($op) {
                                    return Carbon::parse($op->created_at)->format('M');
                                });

        return response()->json(['operations' => $operations], 200);
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
