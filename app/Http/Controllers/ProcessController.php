<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProcessController extends Controller
{
    public function create(Request $request): View
    {
        $folders = DB::table('folders')
            ->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();

        $client = null;
        if ($request->client_id) {
            $client = DB::table('clients')
                ->where('id', $request->client_id)->whereNull('deleted_at')->first();
        }

        return view('pages.processes.create', compact('folders', 'client'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'client_id'    => 'required|integer|exists:clients,id',
            'folder_id'    => 'nullable|integer|exists:folders,id',
            'number'       => 'required|string|max:100|unique:processes,number',
            'date'         => 'required|date',
            'active_pole'  => 'required|string|max:255',
            'passive_pole' => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
        ], [
            'client_id.required'    => 'Selecione o cliente.',
            'number.required'       => 'Informe o número do processo.',
            'number.unique'         => 'Este número já está cadastrado.',
            'date.required'         => 'Informe a data.',
            'active_pole.required'  => 'Informe o polo ativo.',
            'passive_pole.required' => 'Informe o polo passivo.',
        ]);

        $id = DB::table('processes')->insertGetId([
            'client_id'    => $request->client_id,
            'folder_id'    => $request->folder_id ?: null,
            'number'       => $request->number,
            'date'         => $request->date,
            'active_pole'  => strtoupper($request->active_pole),
            'passive_pole' => strtoupper($request->passive_pole),
            'location'     => $request->location,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'id' => $id]);
        }

        return redirect()->route('processos.show', $id)
            ->with('success', 'Processo cadastrado com sucesso!');
    }

    public function show(int $id): View
    {
        $process = DB::table('processes')
            ->leftJoin('clients', 'clients.id', '=', 'processes.client_id')
            ->leftJoin('folders', 'folders.id', '=', 'processes.folder_id')
            ->select('processes.*', 'clients.name as client_name', 'folders.name as folder_name', 'folders.color as folder_color')
            ->where('processes.id', $id)
            ->whereNull('processes.deleted_at')
            ->firstOrFail();

        return view('pages.processes.show', compact('process'));
    }

    public function search(Request $request): View
    {
        $number       = $request->get('number', '');
        $date         = $request->get('date', '');
        $passivePole  = $request->get('passive_pole', '');
        $folderId     = $request->get('folder_id', '');
        $client       = $request->get('client', '');

        $folders = DB::table('folders')
            ->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();

        $processes = null;
        $hasFilter = $number || $date || $passivePole || $folderId || $client;

        if ($hasFilter) {
            $processes = DB::table('processes')
                ->leftJoin('clients', 'clients.id', '=', 'processes.client_id')
                ->leftJoin('folders', 'folders.id', '=', 'processes.folder_id')
                ->select('processes.*', 'clients.name as client_name', 'folders.name as folder_name', 'folders.color as folder_color')
                ->whereNull('processes.deleted_at')
                ->when($number,      fn($q) => $q->where('processes.number', 'like', "%{$number}%"))
                ->when($date,        fn($q) => $q->whereDate('processes.date', $date))
                ->when($passivePole, fn($q) => $q->where('processes.passive_pole', 'like', '%' . strtoupper($passivePole) . '%'))
                ->when($folderId,    fn($q) => $q->where('processes.folder_id', $folderId))
                ->when($client,      fn($q) => $q->where('clients.name', 'like', '%' . strtoupper($client) . '%'))
                ->orderByDesc('processes.date')
                ->get();
        }

        return view('pages.processes.search', compact('processes', 'folders', 'number', 'date', 'passivePole', 'folderId', 'client', 'hasFilter'));
    }

    public function edit(int $id): View
    {
        $process = DB::table('processes')->where('id', $id)->whereNull('deleted_at')->firstOrFail();
        $folders = DB::table('folders')->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
        $client  = DB::table('clients')->where('id', $process->client_id)->first();

        return view('pages.processes.edit', compact('process', 'folders', 'client'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'folder_id'    => 'nullable|integer|exists:folders,id',
            'number'       => 'required|string|max:100|unique:processes,number,' . $id,
            'date'         => 'required|date',
            'active_pole'  => 'required|string|max:255',
            'passive_pole' => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
        ]);

        DB::table('processes')->where('id', $id)->update([
            'folder_id'    => $request->folder_id ?: null,
            'number'       => $request->number,
            'date'         => $request->date,
            'active_pole'  => strtoupper($request->active_pole),
            'passive_pole' => strtoupper($request->passive_pole),
            'location'     => $request->location,
            'updated_at'   => now(),
        ]);

        return redirect()->route('processos.show', $id)->with('success', 'Processo atualizado!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $process = DB::table('processes')->where('id', $id)->whereNull('deleted_at')->firstOrFail();
        DB::table('processes')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->route('clientes.show', $process->client_id)->with('success', 'Processo removido.');
    }

    public function byClient(int $clientId): JsonResponse
    {
        $processes = DB::table('processes')
            ->leftJoin('folders', 'folders.id', '=', 'processes.folder_id')
            ->select('processes.*', 'folders.name as folder_name', 'folders.color as folder_color')
            ->where('processes.client_id', $clientId)
            ->whereNull('processes.deleted_at')
            ->orderByDesc('processes.date')
            ->get();

        return response()->json($processes);
    }
}
