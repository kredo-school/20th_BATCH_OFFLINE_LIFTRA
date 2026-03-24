<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    // 動作確認用？
            public function index()
        {
            return response()->json(['message' => 'Milestones index']);
        }

        public function store(Request $request)
        {
            return response()->json(['message' => 'Milestone stored']);
        }

        public function show(string $id)
        {
            return response()->json(['message' => "Milestone {$id} details"]);
        }

        public function update(Request $request, string $id)
        {
            return response()->json(['message' => "Milestone {$id} updated"]);
        }

        public function destroy(string $id)
        {
            return response()->json(['message' => "Milestone {$id} deleted"]);
        }
    }
