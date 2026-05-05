<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Lista agendamentos de um processo.
     */
    public function byProcess(int $processId): JsonResponse
    {
        $schedules = DB::table('schedules')
            ->leftJoin('schedule_types', 'schedule_types.id', '=', 'schedules.schedule_type_id')
            ->leftJoin('users as creator', 'creator.id', '=', 'schedules.created_by')
            ->leftJoin('users as completer', 'completer.id', '=', 'schedules.completed_by')
            ->where('schedules.process_id', $processId)
            ->whereNull('schedules.deleted_at')
            ->select(
                'schedules.*',
                'schedule_types.name as type_name',
                'schedule_types.color as type_color',
                'creator.name as created_by_name',
                'completer.name as completed_by_name'
            )
            ->orderByDesc('schedules.date')
            ->orderByDesc('schedules.time')
            ->get()
            ->map(function ($s) {
                // Carrega destinatários
                $s->recipients = DB::table('schedule_users')
                    ->join('users', 'users.id', '=', 'schedule_users.user_id')
                    ->where('schedule_users.schedule_id', $s->id)
                    ->select('users.id', 'users.name', 'users.avatar')
                    ->get();
                return $s;
            });

        return response()->json($schedules);
    }

    /**
     * Cria um novo agendamento.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'process_id'       => 'required|integer|exists:processes,id',
            'schedule_type_id' => 'nullable|integer|exists:schedule_types,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'date'             => 'required|date',
            'time'             => 'required',
            'recipient_ids'    => 'required|array|min:1',
            'recipient_ids.*'  => 'integer|exists:users,id',
        ], [
            'title.required'         => 'O título é obrigatório.',
            'date.required'          => 'A data é obrigatória.',
            'time.required'          => 'A hora é obrigatória.',
            'recipient_ids.required' => 'Selecione ao menos um destinatário.',
            'recipient_ids.min'      => 'Selecione ao menos um destinatário.',
        ]);

        $id = DB::table('schedules')->insertGetId([
            'process_id'       => $request->process_id,
            'schedule_type_id' => $request->schedule_type_id,
            'created_by'       => auth()->id(),
            'title'            => strtoupper($request->title),
            'description'      => $request->description,
            'date'             => $request->date,
            'time'             => $request->time,
            'status'           => 'open',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Insere destinatários
        foreach ($request->recipient_ids as $userId) {
            DB::table('schedule_users')->insert([
                'schedule_id' => $id,
                'user_id'     => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return response()->json(['success' => true, 'id' => $id]);
    }

    /**
     * Conclui um agendamento — apenas destinatários.
     */
    public function complete(int $id): JsonResponse
    {
        $schedule = DB::table('schedules')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$schedule) return response()->json(['error' => 'not_found'], 404);

        $isRecipient = DB::table('schedule_users')
            ->where('schedule_id', $id)
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isRecipient) {
            return response()->json(['error' => 'forbidden', 'message' => 'Apenas destinatários podem concluir este agendamento.'], 403);
        }

        DB::table('schedules')->where('id', $id)->update([
            'status'       => 'done',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Reabre um agendamento — quem criou ou quem concluiu.
     */
    public function reopen(int $id): JsonResponse
    {
        $schedule = DB::table('schedules')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$schedule) return response()->json(['error' => 'not_found'], 404);

        $canReopen = auth()->id() === $schedule->created_by
            || auth()->id() === $schedule->completed_by;

        if (!$canReopen) {
            return response()->json(['error' => 'forbidden', 'message' => 'Apenas quem criou ou concluiu pode reabrir.'], 403);
        }

        DB::table('schedules')->where('id', $id)->update([
            'status'       => 'open',
            'completed_by' => null,
            'completed_at' => null,
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove um agendamento — apenas criador.
     */
    public function destroy(int $id): JsonResponse
    {
        $schedule = DB::table('schedules')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$schedule) return response()->json(['error' => 'not_found'], 404);

        if (auth()->id() !== $schedule->created_by) {
            return response()->json(['error' => 'forbidden', 'message' => 'Apenas quem criou pode remover.'], 403);
        }

        DB::table('schedule_users')->where('schedule_id', $id)->delete();
        DB::table('schedules')->where('id', $id)->update(['deleted_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Busca usuários com acesso ao módulo de processos (que está dentro de clientes).
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        // Usuários que têm perfil com acesso ao módulo 'clientes' (onde processos está)
        $moduleId = DB::table('modules')->where('slug', 'clientes')->value('id');

        $query = DB::table('users')
            ->join('profile_user', 'profile_user.user_id', '=', 'users.id')
            ->join('profile_module', 'profile_module.profile_id', '=', 'profile_user.profile_id')
            ->where('profile_module.module_id', $moduleId)
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->select('users.id', 'users.name')
            ->distinct();

        if ($q) {
            $query->where('users.name', 'like', "%{$q}%");
        }

        return response()->json($query->orderBy('users.name')->get());
    }
}
