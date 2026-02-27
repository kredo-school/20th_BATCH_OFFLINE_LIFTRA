<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    // Googleログイン画面にリダイレクト
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Googleから戻ってきたときの処理
    public function handleGoogleCallback()
    {
        $provider = Socialite::driver('google');

        /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
        $googleUser = $provider->stateless()->user();

        // メールでユーザーを検索
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(uniqid()), // パスワードはランダム
            ]
        );

        Auth::login($user);

        return redirect('/home'); // ログイン後にリダイレクト
    }
}
