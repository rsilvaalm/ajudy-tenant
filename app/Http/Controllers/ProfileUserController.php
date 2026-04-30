<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileUserController extends Controller
{
    public function show(): View
    {
        $user = DB::table('users')->where('id', auth()->id())->first();

        $profile = DB::table('profile_user')
            ->join('profiles', 'profiles.id', '=', 'profile_user.profile_id')
            ->where('profile_user.user_id', auth()->id())
            ->select('profiles.name as profile_name')
            ->first();

        return view('pages.profile.show', compact('user', 'profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
            'oab'    => 'nullable|string|max:20',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'avatar.image'  => 'O arquivo deve ser uma imagem.',
            'avatar.max'    => 'A imagem deve ter no máximo 2MB.',
            'avatar.mimes'  => 'Use JPG, PNG ou WebP.',
        ]);

        $user = DB::table('users')->where('id', auth()->id())->first();

        $data = [
            'name'       => $request->name,
            'updated_at' => now(),
        ];

        // OAB apenas se for advogado
        if ($user->is_lawyer) {
            $data['oab'] = $request->oab;
        }

        // Upload do avatar
        if ($request->hasFile('avatar')) {
            // Remove avatar anterior
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file      = $request->file('avatar');
            $filename  = 'avatar_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $directory = public_path('uploads/avatars');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $file->move($directory, $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        DB::table('users')->where('id', auth()->id())->update($data);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\#])[A-Za-z\d@$!%*?&\#]{8,}$/',
            ],
        ], [
            'current_password.required' => 'Informe a senha atual.',
            'password.required'         => 'A nova senha é obrigatória.',
            'password.min'              => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed'        => 'As senhas não conferem.',
            'password.regex'            => 'A senha deve conter: maiúscula, minúscula, número e caractere especial (@$!%*?&#).',
        ]);

        $user = DB::table('users')->where('id', auth()->id())->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        DB::table('users')->where('id', auth()->id())->update([
            'password'   => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }
}
