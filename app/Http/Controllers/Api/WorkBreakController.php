<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WorkBreak\StoreWorkBreakRequest;
use App\Models\WorkBreak;
use Illuminate\Http\Request;

class WorkBreakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workBreaks = WorkBreak::all();
        if ($workBreaks->isEmpty()){
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum Intervalo de Trabalho encontrado.']);
        }
        return response()->json($workBreaks, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkBreakRequest $request)
    {
        $validatedData = $request->validated();
        $workBreak = WorkBreak::create($validatedData);
        return response()->json($workBreak, 201);   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
