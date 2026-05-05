<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceListController extends Controller
{
    /**
     * Listagem geral de atendimentos com filtro de período e busca.
     */
    public function index(Request $request): View
    {
        $search    = $request->get('q', '');
        $dateFrom  = $request->get('from', now()->subDays(7)->format('Y-m-d'));
        $dateTo    = $request->get('to',   now()->format('Y-m-d'));

        $attendances = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->select(
                'attendances.*',
                'users.name as user_name'
            )
            ->whereNull('attendances.deleted_at')
            ->whereBetween('attendances.date', [$dateFrom, $dateTo])
            ->when($search, fn($q) => $q
                ->where('attendances.client_name', 'like', "%{$search}%")
                ->orWhere('users.name', 'like', "%{$search}%"))
            ->orderByDesc('attendances.date')
            ->orderByDesc('attendances.start_time')
            ->paginate(20)
            ->withQueryString();

        return view('pages.attendances.index', compact('attendances', 'search', 'dateFrom', 'dateTo'));
    }

    /**
     * Retorna os dados de um atendimento via JSON (para o modal).
     */
    public function show(int $id): JsonResponse
    {
        $attendance = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->select('attendances.*', 'users.name as user_name')
            ->where('attendances.id', $id)
            ->whereNull('attendances.deleted_at')
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'not_found'], 404);
        }

        return response()->json($attendance);
    }

    /**
     * Remove um atendimento (soft delete) — somente o autor.
     */
    public function destroy(int $id): JsonResponse
    {
        $attendance = DB::table('attendances')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'not_found'], 404);
        }

        if ($attendance->user_id !== auth()->id()) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        DB::table('attendances')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Atendimentos de um cliente específico (para a página do cliente).
     * Paginação por demanda via AJAX.
     */
    public function byClient(Request $request, int $clientId): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = 5;

        $total = DB::table('attendances')
            ->where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->count();

        $items = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->where('attendances.client_id', $clientId)
            ->whereNull('attendances.deleted_at')
            ->select('attendances.*', 'users.name as user_name')
            ->orderByDesc('attendances.date')
            ->orderByDesc('attendances.start_time')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'has_more' => ($page * $perPage) < $total,
        ]);
    }
}
