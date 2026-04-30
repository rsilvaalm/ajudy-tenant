<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('q');
        $rg     = $request->get('rg');
        $cpf    = $request->get('cpf');

        $clients = null;

        if ($search || $rg || $cpf) {
            $clients = DB::table('clients')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->when($rg,     fn($q) => $q->where('rg', 'like', "%{$rg}%"))
                ->when($cpf,    fn($q) => $q->where('cpf', 'like', "%{$cpf}%"))
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        return view('pages.clients.index', compact('clients', 'search', 'rg', 'cpf'));
    }

    public function create(): View
    {
        $customFields = DB::table('custom_fields')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->get();

        return view('pages.clients.create', compact('customFields'));
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $clientId = DB::table('clients')->insertGetId([
            'name'                 => strtoupper($request->name),
            'email'                => $request->email,
            'profession'           => $request->profession,
            'birth_date'           => $request->birth_date,
            'father_name'          => $request->father_name,
            'mother_name'          => $request->mother_name,
            'phone'                => $request->phone,
            'mobile'               => $request->mobile,
            'rg'                   => $request->rg,
            'cpf'                  => $request->cpf,
            'marital_status'       => $request->marital_status,
            'nationality'          => $request->nationality ?? 'Brasileiro(a)',
            'address_street'       => $request->address_street,
            'address_neighborhood' => $request->address_neighborhood,
            'address_city'         => $request->address_city,
            'address_state'        => $request->address_state,
            'address_zip'          => $request->address_zip,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        // Salva campos personalizados
        $this->saveCustomFields($clientId, $request->custom_fields ?? []);

        return redirect()->route('clientes.show', $clientId)
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(int $id): View
    {
        $client = DB::table('clients')->where('id', $id)->whereNull('deleted_at')->firstOrFail();

        // Campos personalizados com valores
        $customFields = DB::table('custom_fields')
            ->leftJoin('client_custom_values', function ($join) use ($id) {
                $join->on('client_custom_values.custom_field_id', '=', 'custom_fields.id')
                     ->where('client_custom_values.client_id', '=', $id);
            })
            ->where('custom_fields.is_active', true)
            ->whereNull('custom_fields.deleted_at')
            ->select('custom_fields.*', 'client_custom_values.value')
            ->orderBy('custom_fields.order')
            ->get();

        // Calcula idade
        $age = $client->birth_date
            ? \Carbon\Carbon::parse($client->birth_date)->age
            : null;

        return view('pages.clients.show', compact('client', 'customFields', 'age'));
    }

    public function edit(int $id): View
    {
        $client = DB::table('clients')->where('id', $id)->whereNull('deleted_at')->firstOrFail();

        $customFields = DB::table('custom_fields')
            ->leftJoin('client_custom_values', function ($join) use ($id) {
                $join->on('client_custom_values.custom_field_id', '=', 'custom_fields.id')
                     ->where('client_custom_values.client_id', '=', $id);
            })
            ->where('custom_fields.is_active', true)
            ->whereNull('custom_fields.deleted_at')
            ->select('custom_fields.*', 'client_custom_values.value')
            ->orderBy('custom_fields.order')
            ->get();

        return view('pages.clients.edit', compact('client', 'customFields'));
    }

    public function update(UpdateClientRequest $request, int $id): RedirectResponse
    {
        DB::table('clients')->where('id', $id)->update([
            'name'                 => strtoupper($request->name),
            'email'                => $request->email,
            'profession'           => $request->profession,
            'birth_date'           => $request->birth_date,
            'father_name'          => $request->father_name,
            'mother_name'          => $request->mother_name,
            'phone'                => $request->phone,
            'mobile'               => $request->mobile,
            'rg'                   => $request->rg,
            'cpf'                  => $request->cpf,
            'marital_status'       => $request->marital_status,
            'nationality'          => $request->nationality ?? 'Brasileiro(a)',
            'address_street'       => $request->address_street,
            'address_neighborhood' => $request->address_neighborhood,
            'address_city'         => $request->address_city,
            'address_state'        => $request->address_state,
            'address_zip'          => $request->address_zip,
            'updated_at'           => now(),
        ]);

        $this->saveCustomFields($id, $request->custom_fields ?? []);

        return redirect()->route('clientes.show', $id)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(int $id): RedirectResponse
    {
        DB::table('clients')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    private function saveCustomFields(int $clientId, array $fields): void
    {
        foreach ($fields as $fieldId => $value) {
            DB::table('client_custom_values')->updateOrInsert(
                ['client_id' => $clientId, 'custom_field_id' => $fieldId],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
