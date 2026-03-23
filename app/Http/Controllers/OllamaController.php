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

        $model = $request->model ?? 'translategemma:4b';
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

        // 目標 (Lifeplan)
        $goals = $user->goals;
        $goalStrings = $goals->map(fn($g) => "- {$g->title} " . ($g->target_date ? "(期限: {$g->target_date})" : ''))->join("\n");

        // --- ポイント1: AIの性格を定義する ---
$systemPrompt = "You are a dedicated Life Plan Coach for the user.
Please respond based on the following [Rules] and [User Data].

[Rules]
1. Do not invent or hallucinate any schedules or habits that are not in the User Data.
2. If you cannot answer based on the provided data, clearly state 'That information is not registered.'
3. Please respond in English.
4. Call the user '{$user->name}'.
5. [Database Integration] Only if the user asks to add a new task or habit (e.g., 'Add a new task', 'Create a habit'), respond ONLY with a pure JSON string in the following format (do not include any other text or Markdown like ```json):
   For tasks: {\"action\": \"create_task\", \"title\": \"Task Name\", \"date\": \"YYYY-MM-DD\"}
   For habits: {\"action\": \"create_habit\", \"title\": \"Habit Name\", \"time\": \"HH:mm\"}

[User Data]
■ Goals: " . ($goalStrings ?: 'Not set') . "
■ Habits: " . ($activeHabits ?: 'Not set') . "
■ Active Tasks: " . ($taskStrings ?: 'None') . "

[Today's Date]
" . now()->format('Y/m/d (D)') . "
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
}

