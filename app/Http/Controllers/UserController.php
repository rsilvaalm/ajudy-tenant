<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Notifications\SetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    private const SYSTEM_EMAIL = 'admin@ajudy.com.br';

    public function index(Request $request): View
    {
        $search = $request->get('search');

        $users = DB::table('users')
            ->leftJoin('profile_user', 'profile_user.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'profiles.id', '=', 'profile_user.profile_id')
            ->select('users.*', 'profiles.name as profile_name')
            ->where('users.email', '!=', self::SYSTEM_EMAIL)
            ->when($search, fn($q) => $q
                ->where('users.name', 'like', "%{$search}%")
                ->orWhere('users.email', 'like', "%{$search}%"))
            ->whereNull('users.deleted_at')
            ->orderBy('users.name')
            ->paginate(10)
            ->withQueryString();

        return view('pages.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $profiles = DB::table('profiles')
            ->where('is_active', true)
            ->where('is_system', false)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('pages.users.create', compact('profiles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userId = DB::table('users')->insertGetId([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make(Str::random(32)),
            'is_active'  => false,
            'is_lawyer'  => $request->boolean('is_lawyer'),
            'oab'        => $request->boolean('is_lawyer') ? $request->oab : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('profile_user')->insert([
            'user_id'    => $userId,
            'profile_id' => $request->profile_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gera token e envia e-mail
        $token = Str::random(64);
        DB::table('password_set_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => $token,
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $tenantName = view()->shared('currentTenant')->name ?? config('app.name');

        try {
            $notifiable = new class($request->name, $request->email) {
                use \Illuminate\Notifications\Notifiable;
                public string $name;
                public string $email;
                public function __construct(string $name, string $email) {
                    $this->name  = $name;
                    $this->email = $email;
                }
                public function routeNotificationForMail(): string { return $this->email; }
            };

            $notifiable->notify(new SetPasswordNotification($token, $tenantName));
            $msg = "Usuário criado! E-mail enviado para {$request->email}.";
        } catch (\Throwable $e) {
            \Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
            $msg = "Usuário criado. Link manual: " . url("/cadastrar-senha?token={$token}&email=" . urlencode($request->email));
        }

        return redirect()->route('usuarios.index')->with('success', $msg);
    }

    public function edit(int $id): View
    {
        $user = DB::table('users')
            ->whereNull('deleted_at')
            ->where('id', $id)
            ->firstOrFail();

        $profiles = DB::table('profiles')
            ->where('is_active', true)
            ->where('is_system', false)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $currentProfileId = DB::table('profile_user')
            ->where('user_id', $id)
            ->value('profile_id');

        return view('pages.users.edit', compact('user', 'profiles', 'currentProfileId'));
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        DB::table('users')->where('id', $id)->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'is_active'  => $request->boolean('is_active', true),
            'is_lawyer'  => $request->boolean('is_lawyer'),
            'oab'        => $request->boolean('is_lawyer') ? $request->oab : null,
            'updated_at' => now(),
        ]);

        DB::table('profile_user')->where('user_id', $id)->delete();
        DB::table('profile_user')->insert([
            'user_id'    => $id,
            'profile_id' => $request->profile_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($id === auth()->id()) {
            return back()->with('error', 'Você não pode remover seu próprio usuário.');
        }

        $user = DB::table('users')->where('id', $id)->first();

        // Hash do e-mail para liberar para novo cadastro preservando referência
        $hashedEmail = 'deleted_' . substr(md5($user->email . $id), 0, 8) . '_' . $user->email;

        DB::table('profile_user')->where('user_id', $id)->delete();
        DB::table('users')->where('id', $id)->update([
            'email'      => $hashedEmail,
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $user = DB::table('users')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update([
            'is_active'  => !$user->is_active,
            'updated_at' => now(),
        ]);

        $status = $user->is_active ? 'desativado' : 'ativado';
        return back()->with('success', "Usuário {$status} com sucesso!");
    }
}
