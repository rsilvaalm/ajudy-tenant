<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleTypeController extends Controller
{
    public function index(): View
    {
        $types = DB::table('schedule_types')->whereNull('deleted_at')->orderBy('name')->get();
        return view('pages.schedules.types.index', compact('types'));
    }

    public function json(): JsonResponse
    {
        return response()->json(
            DB::table('schedule_types')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'color'])
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:schedule_types,name',
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'name.required' => 'O nome do tipo é obrigatório.',
            'name.unique'   => 'Já existe um tipo com este nome.',
        ]);

        DB::table('schedule_types')->insert([
            'name'       => strtoupper($request->name),
            'color'      => $request->color ?? '#1a3c5e',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tipo criado com sucesso!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:schedule_types,name,' . $id,
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        DB::table('schedule_types')->where('id', $id)->update([
            'name'       => strtoupper($request->name),
            'color'      => $request->color ?? '#1a3c5e',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tipo atualizado!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $count = DB::table('schedules')->where('schedule_type_id', $id)->whereNull('deleted_at')->count();
        if ($count > 0) {
            return back()->with('error', "Este tipo possui {$count} agendamento(s) vinculado(s).");
        }

        DB::table('schedule_types')->where('id', $id)->update(['deleted_at' => now()]);
        return back()->with('success', 'Tipo removido!');
    }
}
