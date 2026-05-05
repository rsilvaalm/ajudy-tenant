<?php

namespace App\Http\Controllers;

use App\Services\DatajudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProcessMovementController extends Controller
{
    public function __construct(private DatajudService $datajud) {}

    /**
     * Consulta o DataJud, persiste as movimentações e retorna JSON.
     */
    public function sync(int $processId): JsonResponse
    {
        $process = DB::table('processes')
            ->where('id', $processId)
            ->whereNull('deleted_at')
            ->first();

        if (!$process) {
            return response()->json(['error' => 'Processo não encontrado.'], 404);
        }

        // Consulta DataJud
        $result = $this->datajud->getMovements($process->number);

        if ($result === null) {
            return response()->json([
                'error'   => 'datajud_unavailable',
                'message' => 'Não foi possível consultar o DataJud. Verifique o número do processo ou tente novamente.',
            ], 422);
        }

        if (empty($result)) {
            return response()->json([
                'error'   => 'not_found',
                'message' => 'Processo não encontrado na base do DataJud.',
            ], 404);
        }

        $movements = $result['movements'] ?? [];

        // Persiste novas movimentações (evita duplicatas pelo código + data)
        $saved = 0;
        foreach ($movements as $m) {
            $date        = substr($m['dataHora'] ?? '', 0, 10);
            $code        = (string) ($m['codigo'] ?? '');
            $description = $m['nome'] ?? ($m['complementosTabelados'][0]['nome'] ?? 'Sem descrição');

            if (!$date) continue;

            // Verifica se já existe
            $exists = DB::table('process_movements')
                ->where('process_id', $processId)
                ->where('movement_date', $date)
                ->where('code', $code)
                ->exists();

            if (!$exists) {
                DB::table('process_movements')->insert([
                    'process_id'    => $processId,
                    'movement_date' => $date,
                    'code'          => $code,
                    'description'   => $description,
                    'source'        => 'datajud',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $saved++;
            }
        }

        // Grava data da última sincronização no processo
        DB::table('processes')->where('id', $processId)->update([
            'last_synced_at' => now(),
            'updated_at'     => now(),
        ]);

        // Retorna movimentações persistidas (todas, não só as novas)
        $stored = DB::table('process_movements')
            ->where('process_id', $processId)
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->get();

        $process = DB::table('processes')->where('id', $processId)->first();

        return response()->json([
            'success'        => true,
            'synced'         => $saved,
            'total'          => $stored->count(),
            'movements'      => $stored,
            'process_data'   => $result['process_data'] ?? null,
            'last_synced_at' => $process->last_synced_at,
        ]);
    }

    /**
     * Retorna movimentações já salvas (sem consultar o DataJud).
     */
    public function index(int $processId): JsonResponse
    {
        $movements = DB::table('process_movements')
            ->where('process_id', $processId)
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->get();

        $process = DB::table('processes')->where('id', $processId)->first();

        return response()->json([
            'movements'      => $movements,
            'last_synced_at' => $process->last_synced_at ?? null,
        ]);
    }
}
