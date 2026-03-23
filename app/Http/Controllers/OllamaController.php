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
$systemPrompt = "あなたはユーザー専用のライフプラン・コーチです。
以下の【厳守ルール】と【ユーザーデータ】をもとに回答してください。

【厳守ルール】
1. ユーザーデータにない予定や習慣は、勝手に創作（捏造）しないでください。
2. データの範囲内で答えられない場合は「その情報は登録されていません」とはっきり伝えてください。
3. 日本語のみを使用し、不自然な英語（Morning Runなど）は避けてください。
4. ユーザーを「{$user->name}さん」と呼んでください。
5. 【データベース連動機能】ユーザーが「新しいタスクを追加して」「習慣を追加して」などアプリへの予定登録を求めた場合のみ、返答は一切の文章を省き、以下の形式の純粋なJSON文字列のみを返してください（Markdownの ```json なども絶対に含めないこと）：
タスク追加の場合: {\"action\": \"create_task\", \"title\": \"タスク名\", \"date\": \"YYYY-MM-DD\"}
習慣追加の場合: {\"action\": \"create_habit\", \"title\": \"習慣名\", \"time\": \"HH:mm\"}

【ユーザーデータ】
■ 進行中の目標: " . ($goalStrings ?: '未設定') . "
■ 現在の習慣 (Habits): " . ($activeHabits ?: '未設定') . "
■ 未完了タスク (Tasks): " . ($taskStrings ?: 'なし') . "

【今日の日付】
" . now()->format('Y年m/d (D)') . "
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

