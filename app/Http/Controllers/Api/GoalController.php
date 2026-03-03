<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Goal;


class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return auth()->user()->goals;

        // return \App\Models\Goal::all(); // ユーザーなしでもとりあえず全Goalsを返す
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
                $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_age' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
        ]);

        $goal = auth()->user()->goals()->create([
            'title' => $request->title,
            'description' => $request->description,
            'target_age' => $request->target_age,
            'category_id' => $request->category_id,
        ]);

        return response()->json($goal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = auth()->user()->goals()->findOrFail($id);

        return response()->json($goal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goal = auth()->user()->goals()->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'target_age' => 'nullable|integer',
        ]);

        $goal->update($request->only([
            'title',
            'description',
            'target_age',
        ]));

        return response()->json($goal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = auth()->user()->goals()->findOrFail($id);

        $goal->delete();

        return response()->json([
            'message' => 'Goal deleted successfully.'
        ]);
    }
}
