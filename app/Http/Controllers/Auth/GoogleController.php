<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    // Googleログイン画面にリダイレクト
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/calendar.readonly'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    // Googleから戻ってきたときの処理
    public function handleGoogleCallback()
    {
        $provider = Socialite::driver('google');

        /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
        $googleUser = $provider->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'name' => $googleUser->getName(),
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
            ]);
        } else {
            $user = User::create([
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'password' => Hash::make(Str::random(16)), // ランダムなダミーパスワード
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
            ]);
        }

        Auth::login($user);

        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/home'); // ログイン後にリデリレクト
    }
}
