<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function current(): JsonResponse
    {
        $attendance = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();

        if (!$attendance) return response()->json(null);

        return response()->json([
            'id'          => $attendance->id,
            'client_name' => $attendance->client_name,
            'date'        => $attendance->date,
            'start_time'  => substr($attendance->start_time, 0, 5),
            'notes'       => $attendance->notes,
        ]);
    }

    public function searchClients(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) return response()->json([]);

        return response()->json(
            DB::table('clients')
                ->where('name', 'like', "%{$q}%")
                ->whereNull('deleted_at')
                ->select('id', 'name', 'email', 'mobile', 'phone', 'cpf')
                ->orderBy('name')
                ->limit(8)
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        // Bloqueia segundo atendimento aberto
        $existing = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return response()->json([
                'error'       => 'already_open',
                'id'          => $existing->id,
                'client_name' => $existing->client_name,
                'start_time'  => substr($existing->start_time, 0, 5),
            ], 409);
        }

        $request->validate([
            'client_type'  => 'required|in:existing,new',
            'client_id'    => 'required_if:client_type,existing|nullable|integer',
            'client_name'  => 'required_if:client_type,new|nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'date'         => 'required|date',
            'start_time'   => 'required',
        ]);

        if ($request->client_type === 'existing' && $request->client_id) {
            $client      = DB::table('clients')->find($request->client_id);
            $clientName  = $client->name;
            $clientEmail = $client->email;
            $clientPhone = $client->mobile ?? $client->phone;
        } else {
            $clientName  = strtoupper($request->client_name);
            $clientEmail = $request->client_email;
            $clientPhone = $request->client_phone;
        }

        $id = DB::table('attendances')->insertGetId([
            'client_id'    => $request->client_type === 'existing' ? $request->client_id : null,
            'user_id'      => auth()->id(),
            'client_name'  => $clientName,
            'client_email' => $clientEmail,
            'client_phone' => $clientPhone,
            'date'         => $request->date,
            'start_time'   => $request->start_time,
            'status'       => 'open',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'id'          => $id,
            'client_name' => $clientName,
            'date'        => $request->date,
            'start_time'  => $request->start_time,
        ]);
    }

    public function close(Request $request, int $id): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string']);

        $isAutosave = $request->boolean('autosave', false);

        $data = [
            'notes'      => $request->notes,
            'updated_at' => now(),
        ];

        // Autosave: salva notas sem encerrar
        // Encerramento definitivo: muda status e registra hora de fim
        if (!$isAutosave) {
            $data['end_time'] = now()->format('H:i:s');
            $data['status']   = 'closed';
        }

        DB::table('attendances')->where('id', $id)->update($data);

        return response()->json(['success' => true]);
    }
}
