<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class LifeplanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_id' => 'required|exists:colors,id',
            'icon_id' => 'required|exists:icons,id',
        ]);

        $validated['user_id'] = Auth::id();
        Category::create($validated);

        return redirect()->route('home')->with('success', 'Category added successfully!');
    }
}
