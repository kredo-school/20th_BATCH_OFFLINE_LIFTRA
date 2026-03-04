<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCertification;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * プロフィール表示
     */
    public function index()
    {
        $user = Auth::user();

        // 教育
        $user->education = UserEducation::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($edu){
                $edu->years = $edu->start_date 
                    ? ($edu->end_date ? $edu->start_date.' ~ '.$edu->end_date : $edu->start_date.' ~ Present') 
                    : '';
                return $edu;
            });

        // 職務経験
        $user->experience = UserExperience::where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($exp){
                $exp->years = $exp->start_date 
                    ? ($exp->currently_working ? $exp->start_date.' ~ Present' : $exp->start_date.' ~ '.$exp->end_date) 
                    : '';
                return $exp;
            });

        // 資格
        $user->certifications = UserCertification::where('user_id', $user->id)
            ->orderBy('obtained_date', 'desc')
            ->pluck('title');

        // スキル
        $user->skills = UserSkill::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->pluck('skill_name');

        return view('profile.index', compact('user'));
    }

    /**
     * 編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'birthday'   => 'nullable|date',
            'linkedin'   => 'nullable|url|max:255',
            'portfolio'  => 'nullable|url|max:255',
            'usersgoal'  => 'nullable|string|max:300',
            'profile_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'timezone'   => 'nullable|string|max:255',
            'language'   => 'nullable|string|max:255',
        ]);

        // 基本情報更新
        $user->name       = $request->name;
        $user->email      = $request->email;
        $user->birthday   = $request->birthday;
        $user->linkedin   = $request->linkedin;
        $user->portfolio  = $request->portfolio;
        $user->usersgoal  = $request->usersgoal;
        $user->timezone   = $request->timezone;
        $user->language   = $request->language;

        // 画像処理（Base64保存）
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($imageData);
            $user->profile_image = $base64;
        }

        $user->save();

        return redirect()
            ->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }
}