<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LockScreenController extends Controller
{
    /**
     * Bloqueia a tela e redireciona para o lock screen.
     */
    public function lock(Request $request): RedirectResponse
    {
        $request->session()->put('screen_locked', true);
        $request->session()->put('lock_user_id', Auth::id());

        return redirect()->route('lock');
    }

    /**
     * Exibe a tela de bloqueio.
     * Headers no-cache impedem que o browser sirva páginas anteriores
     * via botão "Voltar" sem passar pela autenticação.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        if (!$request->session()->get('screen_locked')) {
            return redirect()->route('dashboard');
        }

        $customization = DB::table('customization')->first();

        return response()
            ->view('auth.lock', compact('customization'))
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]);
    }

    /**
     * Desloga o usuário a partir da tela de bloqueio.
     * Limpa a sessão de lock antes de deslogar.
     */
    public function lockLogout(Request $request): RedirectResponse
    {
        $request->session()->forget('screen_locked');
        $request->session()->forget('lock_user_id');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Valida a senha e desbloqueia a tela.
     */
    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Informe sua senha para desbloquear.',
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Senha incorreta.',
            ]);
        }

        $request->session()->forget('screen_locked');
        $request->session()->forget('lock_user_id');

        return redirect()->intended(route('dashboard'));
    }
}