<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSkill;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 新規追加
    public function store(Request $request)
    {
        $request->validate([
            'skill_name' => 'required|string|max:255',
        ]);

        $skill = new UserSkill();
        $skill->user_id = Auth::id();
        $skill->skill_name = $request->skill_name;
        $skill->save();

        return redirect()->back()->with('success', 'Skill added.');
    }

    // 編集更新
    public function update(Request $request, $id)
    {
        $skill = UserSkill::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'skill_name' => 'required|string|max:255',
        ]);

        $skill->skill_name = $request->skill_name;
        $skill->save();

        return redirect()->back()->with('success', 'Skill updated.');
    }

    // 削除
    public function destroy($id)
    {
        $skill = UserSkill::where('user_id', Auth::id())->findOrFail($id);
        $skill->delete();

        return redirect()->back()->with('success', 'Skill deleted.');
    }
}