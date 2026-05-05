<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessNoteController extends Controller
{
    /**
     * Lista anotações de um processo — ordem cronológica descendente.
     */
    public function byProcess(int $processId): JsonResponse
    {
        $notes = DB::table('process_notes')
            ->join('users', 'users.id', '=', 'process_notes.user_id')
            ->where('process_notes.process_id', $processId)
            ->whereNull('process_notes.deleted_at')
            ->select(
                'process_notes.*',
                'users.name as user_name',
                'users.avatar as user_avatar'
            )
            ->orderByDesc('process_notes.noted_at')
            ->get();

        return response()->json($notes);
    }

    /**
     * Cria uma nova anotação.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'process_id' => 'required|integer|exists:processes,id',
            'content'    => 'required|string',
            'noted_at'   => 'required|date',
        ], [
            'content.required'  => 'O conteúdo da anotação é obrigatório.',
            'noted_at.required' => 'A data/hora é obrigatória.',
        ]);

        $id = DB::table('process_notes')->insertGetId([
            'process_id' => $request->process_id,
            'user_id'    => auth()->id(),
            'content'    => $request->content,
            'noted_at'   => $request->noted_at,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $id]);
    }

    /**
     * Edita uma anotação — somente quem criou.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $note = DB::table('process_notes')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$note) return response()->json(['error' => 'not_found'], 404);

        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'forbidden', 'message' => 'Apenas quem criou pode editar.'], 403);
        }

        $request->validate([
            'content'  => 'required|string',
            'noted_at' => 'required|date',
        ]);

        DB::table('process_notes')->where('id', $id)->update([
            'content'    => $request->content,
            'noted_at'   => $request->noted_at,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove uma anotação — somente quem criou.
     */
    public function destroy(int $id): JsonResponse
    {
        $note = DB::table('process_notes')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$note) return response()->json(['error' => 'not_found'], 404);

        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'forbidden', 'message' => 'Apenas quem criou pode remover.'], 403);
        }

        DB::table('process_notes')->where('id', $id)->update(['deleted_at' => now()]);

        return response()->json(['success' => true]);
    }
}
