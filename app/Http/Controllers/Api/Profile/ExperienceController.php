<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserExperience;

class ExperienceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 新規追加
    public function store(Request $request)
    {
        $request->validate([
            'job_title'       => 'required|string|max:255',
            'company_name'    => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'currently_working' => 'boolean',
            'description'     => 'nullable|string',
        ]);

        $experience = new UserExperience();
        $experience->user_id = Auth::id();
        $experience->job_title = $request->job_title;
        $experience->company_name = $request->company_name;
        $experience->employment_type = $request->employment_type;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->currently_working = $request->currently_working ?? false;
        $experience->description = $request->description;
        $experience->save();

        return redirect()->back()->with('success', 'Experience added.');
    }

    // 編集更新
    public function update(Request $request, $id)
    {
        $experience = UserExperience::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'job_title'       => 'required|string|max:255',
            'company_name'    => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'currently_working' => 'boolean',
            'description'     => 'nullable|string',
        ]);

        $experience->job_title = $request->job_title;
        $experience->company_name = $request->company_name;
        $experience->employment_type = $request->employment_type;
        $experience->start_date = $request->start_date;
        $experience->end_date = $request->end_date;
        $experience->currently_working = $request->currently_working ?? false;
        $experience->description = $request->description;
        $experience->save();

        return redirect()->back()->with('success', 'Experience updated.');
    }

    // 削除
    public function destroy($id)
    {
        $experience = UserExperience::where('user_id', Auth::id())->findOrFail($id);
        $experience->delete();

        return redirect()->back()->with('success', 'Experience deleted.');
    }
}