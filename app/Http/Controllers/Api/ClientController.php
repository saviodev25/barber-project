<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Models\Client;

use Illuminate\Http\JsonResponse;


class ClientController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // $clients = Client::orderBy('name')->paginate(15);
        $clients = Client::all();
        
        if (!$clients) {
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum cliente encontrado.']);
        }
        return response()->json($clients);
    }


    public function store(StoreClientRequest $request): JsonResponse
    {
        $validateData = $request->validated();
        $client = Client::create($validateData);
        
        return response()->json($client, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $validateData = $request->validated();

        $client->update($validateData);

        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Cliente atualizado com sucesso.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if ($client) {
            $client->delete();
            return response()->json(['status' => 'ok', 'mensagem' => 'Cliente removido com sucesso.']);
        }

        return response()->json(['status' => 'erro', 'mensagem' => 'Cliente não encontrado.'], 404);
    }
}
