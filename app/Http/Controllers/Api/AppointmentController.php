<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Api\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\WorkBreak;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::all();
        if ($appointments->isEmpty()){
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum Agendamento encontrado.']);
        }
        return response()->json($appointments, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $validateData = $request->validated();
        $serviceId = $validateData['service_id'];
        // 3. Criamos um objeto Carbon a partir do start_time
        $startTime = Carbon::parse($validateData['start_time']);
        $endTime = $startTime->copy()->addMinutes($serviceId->duration_minutes);

        $appointmentData = array_merge(
            $validateData,
            ['end_time' => $endTime]
        );


        $appointment = Appointment::create($appointmentData);
        return response()->json($appointment->load('service', 'client', 'user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return response()->json($appointment, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $validateData = $request->validated();
        $appointment->update($validateData);
        
        return response()->json(
            [
                'message' => 'Appointment update Sucessfull!',
                $appointment, 
                201
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $appointmentId = Appointment::findOrFail($id);
        $appointmentId->destroy();

        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Serviço removido com sucesso.'
            ]
        );
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        // 1) Validação
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'user_id' => 'required|integer|exists:users,id',
            'service_id' => 'required|integer|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dados = $validator->validated();
        $data = Carbon::parse($dados['date']);
        $profissionalId = $dados['user_id'];
        $servicoId = $dados['service_id'];

        // 2) Horário de trabalho do profissional
        $horarioTrabalho = WorkSchedule::where('user_id', $profissionalId)
            ->where('day_of_week', $data->dayOfWeek)
            ->first();

        if (!$horarioTrabalho) {
            return response()->json(['available_slots' => ['Barbeiro sem agenda!']]);
        }

        $inicioDoExpediente = $data->copy()->setTimeFromTimeString($horarioTrabalho->start_time);
        $fimDoExpediente = $data->copy()->setTimeFromTimeString($horarioTrabalho->end_time);

        // 3) Pausas cadastradas no banco
        $pausas = WorkBreak::where('user_id', $profissionalId)
            ->where('day_of_week', $data->dayOfWeek)
            ->get();

        // 4) Serviço
        $servico = Service::findOrFail($servicoId);
        $duracaoDoServicoMin = $servico->duration_minutes;

        // 5) Agendamentos ativos
        $agendamentosDoDia = Appointment::where('user_id', $profissionalId)
            ->whereDate('start_time', $data)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        // 6) Configurações extras
        $intervaloMin = 15; 
        // tempo mínimo antes do horário atual
        // $antecedenciaMin = 60; 
        $agora = Carbon::now();

        $horariosDisponiveis = [];
        $inicioPossivel = $inicioDoExpediente->copy();

        while ($inicioPossivel < $fimDoExpediente) {
            $fimPossivel = $inicioPossivel->copy()->addMinutes($duracaoDoServicoMin);

            // Não passa do expediente
            if ($fimPossivel > $fimDoExpediente) {
                break;
            }

            $estaDisponivel = true;

            // 6.1) Bloqueia horários no passado (se a data é hoje)
            if ($data->isToday() && $inicioPossivel->lt($agora)) {
                $estaDisponivel = false;
            }

            // 6.2) Bloqueia horários sem antecedência mínima
            // if ($inicioPossivel->lt($agora->copy()->addMinutes($antecedenciaMin))) {
            //     $estaDisponivel = false;
            // }

            // 6.3) Colisão com pausas
            if ($estaDisponivel) {
                foreach ($pausas as $pausa) {
                    $inicioPausa = $data->copy()->setTimeFromTimeString($pausa->start_time);
                    $fimPausa = $data->copy()->setTimeFromTimeString($pausa->end_time);

                    if ($inicioPossivel < $fimPausa && $fimPossivel > $inicioPausa) {
                        $estaDisponivel = false;
                        break;
                    }
                }
            }

            // 6.4) Colisão com agendamentos existentes (ajustado para bordas)
            if ($estaDisponivel) {
                foreach ($agendamentosDoDia as $ag) {
                    $inicioAgendamento = Carbon::parse($ag->start_time);
                    $fimAgendamento = Carbon::parse($ag->end_time);

                    // Considera conflito apenas se realmente houver sobreposição
                    if ($inicioPossivel <= $fimAgendamento && $fimPossivel >= $inicioAgendamento) {
                        $estaDisponivel = false;
                        break;
                    }
                }
            }

            if ($estaDisponivel) {
                $horariosDisponiveis[] = $inicioPossivel->format('H:i');
            }

            $inicioPossivel->addMinutes($intervaloMin);
        }

        return response()->json(
            [
                'available_slots' => $horariosDisponiveis
            ]);
    }

}
