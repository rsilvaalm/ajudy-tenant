<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function index(): View
    {
        $folders = DB::table('folders')->whereNull('deleted_at')->orderBy('name')->get();
        return view('pages.processes.folders.index', compact('folders'));
    }

    /**
     * Retorna pastas ativas via JSON (para o modal de novo processo).
     */
    public function json(): JsonResponse
    {
        $folders = DB::table('folders')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        return response()->json($folders);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:folders,name',
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'name.required' => 'O nome da pasta é obrigatório.',
            'name.unique'   => 'Já existe uma pasta com este nome.',
        ]);

        DB::table('folders')->insert([
            'name'       => $request->name,
            'color'      => $request->color ?? '#1a3c5e',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pasta criada com sucesso!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:folders,name,' . $id,
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        DB::table('folders')->where('id', $id)->update([
            'name'       => $request->name,
            'color'      => $request->color ?? '#1a3c5e',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Pasta atualizada!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $count = DB::table('processes')->where('folder_id', $id)->whereNull('deleted_at')->count();
        if ($count > 0) {
            return back()->with('error', "Esta pasta possui {$count} processo(s) vinculado(s) e não pode ser removida.");
        }

        DB::table('folders')->where('id', $id)->update(['deleted_at' => now()]);
        return back()->with('success', 'Pasta removida!');
    }
}
