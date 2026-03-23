<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/ollama/generate', 'POST', [
    'prompt' => 'test',
    'model' => 'gemma3:4b'
]);

// Try to authenticate as user 1
$user = \App\Models\User::find(1);
if ($user) {
    auth()->login($user);
} else {
    echo "No user found.\n";
}

$response = $kernel->handle($request);
echo "STATUS: " . $response->getStatusCode() . "\n";
echo "CONTENT: " . $response->getContent() . "\n";
