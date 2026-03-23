<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserEducation;

class EducationController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 新規追加
    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'degree'      => 'required|string|max:255',
            'field'       => 'required|string|max:255',
            'country'     => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $education = new UserEducation();
        $education->user_id = Auth::id();
        $education->school_name = $request->school_name;
        $education->degree = $request->degree;
        $education->field = $request->field;
        $education->country = $request->country;
        $education->start_date = $request->start_date;
        $education->end_date = $request->end_date;
        $education->save();

        return redirect()->back()->with('success', 'Education added.');
    }

    // 編集更新
    public function update(Request $request, $id)
    {
        $education = UserEducation::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'school_name' => 'required|string|max:255',
            'degree'      => 'required|string|max:255',
            'field'       => 'required|string|max:255',
            'country'     => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $education->school_name = $request->school_name;
        $education->degree = $request->degree;
        $education->field = $request->field;
        $education->country = $request->country;
        $education->start_date = $request->start_date;
        $education->end_date = $request->end_date;
        $education->save();

        return redirect()->back()->with('success', 'Education updated.');
    }

    // 削除
    public function destroy($id)
    {
        $education = UserEducation::where('user_id', Auth::id())->findOrFail($id);
        $education->delete();

        return redirect()->back()->with('success', 'Education deleted.');
    }
}