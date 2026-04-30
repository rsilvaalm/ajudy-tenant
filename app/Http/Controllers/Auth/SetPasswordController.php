<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SetPasswordController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $token = $request->get('token');
        $email = $request->get('email');

        if (!$token || !$email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link inválido ou expirado.']);
        }

        $record = DB::table('password_set_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Este link expirou ou é inválido. Solicite um novo acesso ao administrador.']);
        }

        $customization = DB::table('customization')->first();

        return view('auth.set-password', compact('token', 'email', 'customization'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\#])[A-Za-z\d@$!%*?&\#]{8,}$/',
            ],
        ], [
            'password.required'  => 'A senha é obrigatória.',
            'password.min'       => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
            'password.regex'     => 'A senha deve conter: maiúscula, minúscula, número e caractere especial (@$!%*?&#).',
        ]);

        $record = DB::table('password_set_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Link expirado. Solicite um novo acesso ao administrador.']);
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password'   => Hash::make($request->password),
                'is_active'  => true,
                'updated_at' => now(),
            ]);

        DB::table('password_set_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Senha cadastrada! Faça login para continuar.');
    }
}
