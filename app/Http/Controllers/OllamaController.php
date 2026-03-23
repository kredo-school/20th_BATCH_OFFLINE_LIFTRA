<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OllamaController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            // モデル名は環境に合わせて柔軟に変更できるよう string だけにするのが楽です
            'model' => 'nullable|string',
        ]);

        // Determine the best available model
        $model = $this->getAvailableModel($request->model ?? 'translategemma:4b');
        $userPrompt = $request->prompt;

        // --- ユーザー情報の取得とコンテキスト作成 ---
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ログインが必要です。',
            ], 401);
        }

        // タスク情報 (未完了の直近10件)
        $tasks = $user->tasks()->where('completed', false)->orderBy('due_date')->limit(10)->get();
        $taskStrings = $tasks->map(fn($t) => "- {$t->title} " . ($t->due_date ? "(期限: {$t->due_date})" : ''))->join("\n");

        // 習慣情報
        $habits = $user->habits;
        $activeHabits = $habits->map(fn($h) => "- {$h->title}")->join("\n");

        // 目標とそのマイルストーン (Goals with Milestones)
        $goals = $user->goals()->with('milestones')->get();
        $goalStrings = $goals->map(function($g) {
            $ms = $g->milestones->map(fn($m) => "  - Milestone: {$m->title}" . ($m->due_date ? " (Due: {$m->due_date->format('Y/m/d')})" : ''))->join("\n");
            return "- Goal: {$g->title}" . ($g->target_age ? " (Target Age: {$g->target_age})" : '') . ($ms ? "\n$ms" : '');
        })->join("\n");

        // カレンダー予定 (Calendar Events - logic for today/upcoming)
        $events = $user->calendarEvents()->where('start_date', '>=', today())->orderBy('start_date')->limit(10)->get();
        $eventStrings = $events->map(fn($e) => "- {$e->title} (Start: " . $e->start_date . ($e->end_date ? " End: " . $e->end_date : '') . ")")->join("\n");

        // --- ポイント1: AIの性格を定義する ---
$systemPrompt = "You are J.A.R.V.I.S., the highly sophisticated, witty, and loyal AI Assistant (inspired by Iron Man).
You address the user as 'Sir' with a refined British charm. 
Your goal is to manage the Owner's life, goals, and schedule with flawless precision and a touch of dry wit.

Current Time: " . now()->format('Y/m/d (D) H:i') . "

[Rules]
1. Maintain a sophisticated, helpful, and slightly witty persona. Use 'Sir' frequently.
2. Do not invent or hallucinate any schedules, habits, or milestones not present in the User Data.
3. If information is missing, say: 'I'm afraid I don't have that in my database, Sir.'
4. Respond in English.
5. **Formatting Rules**: 
   - Use clear bullet points with '•' or '-' for lists.
   - Always put a NEWLINE between items in a list.
   - Ensure a space after words like 'at' when mentioning time (e.g., 'at 08:46').
   - Use white space generously to make the text easy to read for the Owner.
6. **Smart Scheduling**: When a user asks about their schedule or tasks, compare the 'Current Time' with the item's time. 
   - If it is already past the scheduled time or too late to reasonably finish, gracefully suggest a reschedule to a specific logical time/date.
   - Proactively advise on manageability (e.g., 'Owner, considering the current hour, it might be more prudent to move this task to tomorrow morning to ensure peak performance.')
7. [Database Integration] Only for requests to add items, respond ONLY with a pure JSON string (no Markdown):
   Tasks: {\"action\": \"create_task\", \"title\": \"...\", \"date\": \"YYYY-MM-DD\"}
   Habits: {\"action\": \"create_habit\", \"title\": \"...\", \"time\": \"HH:mm\"}

[User Data]
■ Life Goals & Milestones:
" . ($goalStrings ?: 'No goals registered.') . "

■ Today's Calendar Events:
" . ($eventStrings ?: 'No upcoming events.') . "

■ Active Habits: 
" . ($activeHabits ?: 'No active habits.') . "

■ Uncompleted Tasks: 
" . ($taskStrings ?: 'No pending tasks.') . "
";
        try {
            $baseUrl = env('OLLAMA_HOST', 'http://localhost:11434');
            
            return response()->stream(function () use ($baseUrl, $model, $systemPrompt, $userPrompt) {
                // Initialize HTTP client with streaming options
                $response = Http::withOptions([
                    'stream' => true,
                    'timeout' => 60,
                ])->post("{$baseUrl}/api/chat", [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'options' => [
                        'temperature' => 0.1,
                        'top_p' => 0.9,
                    ],
                    'stream' => true, // Enable streaming from Ollama
                ]);

                $body = $response->getBody();
                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    echo $chunk;
                    // Flush buffer to send chunk immediately
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Disable Nginx buffering
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '接続エラー: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dynamically discover and select an available Ollama model.
     */
    private function getAvailableModel($preferredModel)
    {
        $baseUrl = env('OLLAMA_HOST', 'http://localhost:11434');

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/api/tags");
            
            if ($response->successful()) {
                $tags = $response->json()['models'] ?? [];
                $availableModels = array_map(fn($m) => $m['name'], $tags);

                if (empty($availableModels)) {
                    return $preferredModel;
                }

                // 1. Try preferred model
                if (in_array($preferredModel, $availableModels)) {
                    return $preferredModel;
                }

                // 2. Try common gemma models
                foreach ($availableModels as $name) {
                    if (stripos($name, 'gemma') !== false) {
                        return $name;
                    }
                }

                // 3. Try llama or mistral fallbacks
                foreach (['llama3', 'mistral', 'llama2', 'phi'] as $fallback) {
                    foreach ($availableModels as $name) {
                        if (stripos($name, $fallback) !== false) {
                            return $name;
                        }
                    }
                }

                // 4. Default to the first available model
                return $availableModels[0];
            }
        } catch (\Exception $e) {
            // Log error or ignore and use preferred
        }

        return $preferredModel;
    }
}

