<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $profiles = DB::table('profiles')
            ->where('is_system', false) // oculta perfis do sistema
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('pages.profiles.index', compact('profiles', 'search'));
    }

    public function create(): View
    {
        $modules = DB::table('modules')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.profiles.create', compact('modules'));
    }

    public function store(StoreProfileRequest $request): RedirectResponse
    {
        $profileId = DB::table('profiles')->insertGetId([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'is_system'   => false,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        if ($request->module_ids) {
            $inserts = collect($request->module_ids)->map(fn($id) => [
                'profile_id' => $profileId,
                'module_id'  => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('profile_module')->insert($inserts);
        }

        return redirect()->route('perfis.index')
            ->with('success', 'Perfil criado com sucesso!');
    }

    public function edit(int $id): View
    {
        $profile = DB::table('profiles')
            ->where('is_system', false)
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();

        $modules = DB::table('modules')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedModules = DB::table('profile_module')
            ->where('profile_id', $id)
            ->pluck('module_id')
            ->toArray();

        return view('pages.profiles.edit', compact('profile', 'modules', 'selectedModules'));
    }

    public function update(UpdateProfileRequest $request, int $id): RedirectResponse
    {
        $profile = DB::table('profiles')
            ->where('is_system', false)
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();

        DB::table('profiles')->where('id', $id)->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'updated_at'  => now(),
        ]);

        DB::table('profile_module')->where('profile_id', $id)->delete();

        if ($request->module_ids) {
            $inserts = collect($request->module_ids)->map(fn($mid) => [
                'profile_id' => $id,
                'module_id'  => $mid,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('profile_module')->insert($inserts);
        }

        return redirect()->route('perfis.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $profile = DB::table('profiles')
            ->where('is_system', false)
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();

        $usersCount = DB::table('profile_user')->where('profile_id', $id)->count();
        if ($usersCount > 0) {
            return back()->with('error', "Este perfil possui {$usersCount} usuário(s) vinculado(s) e não pode ser removido.");
        }

        DB::table('profile_module')->where('profile_id', $id)->delete();
        DB::table('profiles')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->route('perfis.index')
            ->with('success', 'Perfil removido com sucesso!');
    }
}
