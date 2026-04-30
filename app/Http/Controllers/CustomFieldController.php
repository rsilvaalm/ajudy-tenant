<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    public function index(): View
    {
        $fields = DB::table('custom_fields')
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->get();

        return view('pages.clients.custom-fields.index', compact('fields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'label'    => 'required|string|max:100',
            'required' => 'boolean',
            'order'    => 'integer|min:0',
        ], [
            'label.required' => 'O nome do campo é obrigatório.',
        ]);

        DB::table('custom_fields')->insert([
            'label'      => $request->label,
            'key'        => Str::slug($request->label, '_'),
            'required'   => $request->boolean('required'),
            'order'      => $request->order ?? 0,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Campo criado com sucesso!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'label'    => 'required|string|max:100',
            'required' => 'boolean',
            'order'    => 'integer|min:0',
        ]);

        DB::table('custom_fields')->where('id', $id)->update([
            'label'      => $request->label,
            'required'   => $request->boolean('required'),
            'order'      => $request->order ?? 0,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Campo atualizado!');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $field = DB::table('custom_fields')->find($id);
        DB::table('custom_fields')->where('id', $id)->update([
            'is_active'  => !$field->is_active,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Campo ' . ($field->is_active ? 'desativado' : 'ativado') . '!');
    }

    public function destroy(int $id): RedirectResponse
    {
        DB::table('custom_fields')->where('id', $id)->update(['deleted_at' => now()]);

        return back()->with('success', 'Campo removido!');
    }
}
