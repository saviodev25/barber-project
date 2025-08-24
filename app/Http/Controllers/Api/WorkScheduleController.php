<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WorkSchedule\StoreWorkScheduleRequest;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{

    public function index()
    {
        $workSchedules = WorkSchedule::all();
        if ($workSchedules->isEmpty()){
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum Horário de Trabalho encontrado.']);
        }
        return response()->json($workSchedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkScheduleRequest $request)
    {
        $validated = $request->validated();
        $workSchedule = WorkSchedule::create($validated);
        return response()->json($workSchedule, 201);
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
