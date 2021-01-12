<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

class ClientController extends Controller 
{
    public function index()
    {
        $clients = Client::all();

        return response()->json(['clients' => $clients], 200);
    }

    public function read(Request $request)
    {
        $this->validate($request, ['clientId' => 'required|numeric']);

        $clientId = $request->input('clientId');

        $client = Client::find($clientId);

        if(!$client) {
            return response()->json(['message' => 'client not found!'], 400);
        }

        return response()->json(['client' => $client], 200);
    }

    public function create(Request $request) 
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string'
            ]);

        $client = Client::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number')
        ]);

        return response()->json(['message' => 'CLIENT CREATED!', 'client' => $client], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, ['clientId' => 'required|numeric']);

        Client::where('id', $request->input('clientId'))
                    ->update([
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'phone_number' => $request->input('phone_number')
                    ]);
    
        $client = Client::find($request->input('clientId'));

        return response()->json(['message' => 'CLIENT UPDATED!', 'client' => $client], 200);
    }

    public function delete(Request $request)
    {
        $this->validate($request, ['clientId' => 'required|numeric']);

        $client = Client::find($request->input('clientId'));
        $client->delete();

        return response()->json(['message' => 'CLIENT DELETED!'], 200);
    }
}