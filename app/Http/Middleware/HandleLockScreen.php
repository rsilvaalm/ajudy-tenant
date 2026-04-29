<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleLockScreen
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se a tela está bloqueada e não está na rota de lock, redireciona
        if (
            Auth::check() &&
            $request->session()->get('screen_locked') &&
            !$request->routeIs('lock') &&
            !$request->routeIs('lock.unlock')
        ) {
            return redirect()->route('lock');
        }

        return $next($request);
    }
}
