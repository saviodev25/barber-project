<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WorkSchedule\StoreWorkScheduleRequest;
use App\Http\Requests\Api\WorkSchedule\UpdateWorkScheduleRequest;
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
    public function show($id)
    {
        $workSchedule = WorkSchedule::find($id);
        return response()->json($workSchedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkScheduleRequest $request, WorkSchedule $workSchedule)
    {
        $validated = $request->validated();
        $workSchedule->update($validated);

        return response()->json($workSchedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $workSchedule = WorkSchedule::findOrFail($id);
        $workSchedule->delete();
        
        return response()->json(
            [
                'message' => 'Work Schedule removido com sucesso',
                'work_schedule' => $workSchedule
            ]
        );
    }
}
