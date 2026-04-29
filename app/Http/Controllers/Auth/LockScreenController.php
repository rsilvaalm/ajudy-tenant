<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
     */
    public function show(Request $request): View|RedirectResponse
    {
        // Se não está bloqueado, volta para o dashboard
        if (!$request->session()->get('screen_locked')) {
            return redirect()->route('dashboard');
        }

        $customization = DB::table('customization')->first();

        return view('auth.lock', compact('customization'));
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
