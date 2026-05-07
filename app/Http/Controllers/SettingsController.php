<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        $user = $this->currentUser($request);

        return view('settings.show', compact('user'));
    }

    public function updateUsername(Request $request)
    {
        $user = $this->currentUser($request);

        // Username yang tidak boleh dipakai user (reserved untuk sistem)
        $reserved = ['deleted', '[deleted]', 'admin', 'administrator', 'moderator',
                     'mod', 'system', 'koar', 'root', 'support', 'help'];

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
                function ($attribute, $value, $fail) use ($reserved) {
                    if (in_array(strtolower($value), $reserved, true)) {
                        $fail('Username tersebut tidak tersedia.');
                    }
                },
            ],
        ], [
            'username.regex'  => 'Username hanya boleh huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah dipakai.',
            'username.min'    => 'Username minimal 3 karakter.',
            'username.max'    => 'Username maksimal 30 karakter.',
        ]);

        $user->update(['username' => $validated['username']]);

        return redirect()->route('settings.show')
            ->with('success', 'Username berhasil diubah.');
    }

    public function destroy(Request $request)
    {
        $user = $this->currentUser($request);

        DB::transaction(function () use ($user) {
            $user->update([
                'username'      => '[deleted]',
                'browser_token' => null,
                'is_active'     => false,
            ]);
            $user->delete();
        });

        $response = redirect()->route('home')
            ->with('success', 'Akunmu telah dihapus.');
        $response->withCookie(cookie()->forget('koar_token'));

        return $response;
    }
}
