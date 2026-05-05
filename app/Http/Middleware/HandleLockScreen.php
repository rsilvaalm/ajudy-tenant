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
        if (
            Auth::check() &&
            $request->session()->get('screen_locked') &&
            !$request->routeIs('lock') &&
            !$request->routeIs('lock.unlock') &&
            !$request->routeIs('lock.logout')
        ) {
            return redirect()->route('lock');
        }

        return $next($request);
    }
}