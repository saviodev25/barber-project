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
use Carbon\CarbonTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::all();
        if ($appointments->isEmpty()){
            return response()->json(['status' => 'erro', 'mensagem' => 'Nenhum Agendamento encontrado.']);
        }
        return response()->json($appointments, 201);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $service = Service::find($validatedData['service_id']);

        // 3. Criamos um objeto Carbon a partir do start_time
        $startTime = Carbon::parse($validatedData['start_time']);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        $appointmentData = array_merge(
            $validatedData,
            ['end_time' => $endTime]
        );


        $appointment = Appointment::create($appointmentData);
        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment)
    {
        return response()->json($appointment, 201);
    }

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

    public function destroy($id)
    {
        $appointmentId = Appointment::findOrFail($id);
        $appointmentId->delete();

        return response()->json(
            [
                'status' => 'ok',
                'mensagem' => 'Serviço removido com sucesso.'
            ]
        );
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'user_id' => 'required|integer|exists:users,id',
            'service_id' => 'required|integer|exists:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dados = $validator->validated();
        $dia_do_agendamento = Carbon::parse($dados['date']);
        $profissionalId = $dados['user_id'];
        $servicoId = $dados['service_id'];

        // 2) Horário de trabalho do profissional
        $horarioTrabalho = WorkSchedule::where('user_id', $profissionalId)
            ->where('day_of_week', $dia_do_agendamento->dayOfWeek)
            ->first();

        if (!$horarioTrabalho) {
            return response()->json(['available_slots' => ['Barbeiro sem agenda!']]);
        }

        //Pega a data de agendamento do clinete e vincula o horário de expediente do barbeiro
        //inicio: 2025-09-10 08:00:00
        //Fim: 2025-09-10 18:00:00
        $inicioDoExpediente = $dia_do_agendamento->copy()->setTimeFromTimeString($horarioTrabalho->start_time);
        $fimDoExpediente = $dia_do_agendamento->copy()->setTimeFromTimeString($horarioTrabalho->end_time);

        // Pausas cadastradas do barbeiro
        $pausas = WorkBreak::where('user_id', $profissionalId)
            ->where('day_of_week', $dia_do_agendamento->dayOfWeek)
            ->get();

        // Serviço
        $servico = Service::findOrFail($servicoId);
        $duracaoDoServicoMin = $servico->duration_minutes;

        // Agendamentos ativos
        $agendamentosDoDia = Appointment::where('user_id', $profissionalId)
            ->whereDate('start_time', $dia_do_agendamento)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->get(['start_time', 'end_time']);

        // Configurações extras
        $intervaloMin = 15; 
        // tempo mínimo antes do horário atual
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

            // Bloqueia horários no passado (se a data é hoje)
            if ($dia_do_agendamento->isToday() && $inicioPossivel->lt($agora)) {
                $estaDisponivel = false;
            }

            // Colisão com pausas
            if ($estaDisponivel) {
                foreach ($pausas as $pausa) {
                    $inicioPausa = $dia_do_agendamento->copy()->setTimeFromTimeString($pausa->start_time);
                    $fimPausa = $dia_do_agendamento->copy()->setTimeFromTimeString($pausa->end_time);

                    if ($inicioPossivel < $fimPausa && $fimPossivel > $inicioPausa) {
                        $estaDisponivel = false;
                        break;
                    }
                }
            }

            // Colisão com agendamentos existentes (ajustado para bordas)
            if ($estaDisponivel) {
                foreach ($agendamentosDoDia as $ag) {
                    $inicioAgendamento = Carbon::parse($ag->start_time);
                    $fimAgendamento = Carbon::parse($ag->end_time);

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
            ]
        );
    }

}
