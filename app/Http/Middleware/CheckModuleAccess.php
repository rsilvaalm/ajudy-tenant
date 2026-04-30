<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        $userId = auth()->id();

        // Busca os perfis do usuário
        $profileIds = DB::table('profile_user')
            ->where('user_id', $userId)
            ->pluck('profile_id');

        if ($profileIds->isEmpty()) {
            abort(403, 'Você não tem perfil atribuído.');
        }

        // Verifica se algum perfil tem acesso ao módulo
        $hasAccess = DB::table('profile_module')
            ->join('modules', 'modules.id', '=', 'profile_module.module_id')
            ->whereIn('profile_module.profile_id', $profileIds)
            ->where('modules.slug', $moduleSlug)
            ->where('modules.is_active', true)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Você não tem acesso a este módulo.');
        }

        return $next($request);
    }
}
