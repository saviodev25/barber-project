<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Requests\Api\Service\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        if ($services->isEmpty()){
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum serviço encontrado.']);
        }
        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $validatedData = $request->validated();
        $service = Service::create($validatedData);

        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $validateData = $request->validated();
        $service->update($validateData);

        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Serviço atualizado com sucesso.',
                'dados' => $service
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $serviceId = Service::findOrFail($id);
        $serviceId->delete();
        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Serviço removido com sucesso.'
            ]
        );
    }
}
