<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCertification;

class CertificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 新規追加
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'obtained_date' => 'required|date',
        ]);

        $cert = new UserCertification();
        $cert->user_id = Auth::id();
        $cert->title = $request->title;
        $cert->issuer = $request->issuer;
        $cert->obtained_date = $request->obtained_date;
        $cert->save();

        return redirect()->back()->with('success', 'Certification added.');
    }

    // 編集更新
    public function update(Request $request, $id)
    {
        $cert = UserCertification::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'obtained_date' => 'required|date',
        ]);

        $cert->title = $request->title;
        $cert->issuer = $request->issuer;
        $cert->obtained_date = $request->obtained_date;
        $cert->save();

        return redirect()->back()->with('success', 'Certification updated.');
    }

    // 削除
    public function destroy($id)
    {
        $cert = UserCertification::where('user_id', Auth::id())->findOrFail($id);
        $cert->delete();

        return redirect()->back()->with('success', 'Certification deleted.');
    }
}