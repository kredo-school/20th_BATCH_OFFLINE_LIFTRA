<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function help()
    {
        return view('settings.help');
    }

    public function passwordEdit()
    {
        return view('settings.password');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        // Update the password
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return redirect()->route('settings.index')->with('success', 'Password updated successfully.');
    }

    public function deleteAccount()
    {
        return view('settings.delete-account');
    }

    public function destroyAccount(Request $request)
    {
        $request->validate([
            'email_confirm' => ['required']
        ]);

        $user = auth()->user();

        if ($request->email_confirm !== $user->email) {
            return back()->withErrors(['email_confirm' => 'The email address does not match our records.']);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deleted.');
    }
}
