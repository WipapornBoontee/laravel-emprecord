<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class WebhookController extends Controller
{
    /**
     * Handle incoming GitHub Webhook for Auto Deploy (Git Pull).
     */
    public function handle(Request $request): JsonResponse
    {
        // Handle GET requests (Health check / status)
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'active',
                'message' => 'GitHub Webhook endpoint is online and listening for events.',
            ], 200);
        }

        $secret = config('app.github_webhook_secret', env('GITHUB_WEBHOOK_SECRET'));
        
        // 1. Verify GitHub Signature (if secret is configured)
        if ($secret) {
            $signature = $request->header('X-Hub-Signature-256');
            if (!$signature) {
                Log::warning('[GitHub Webhook] Missing X-Hub-Signature-256 header.');
                return response()->json(['status' => 'error', 'message' => 'Missing signature'], 403);
            }

            $computedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($computedSignature, $signature)) {
                Log::warning('[GitHub Webhook] Invalid signature verification.');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
            }
        }

        // 2. Check GitHub Event
        $event = $request->header('X-GitHub-Event');
        if ($event === 'ping') {
            Log::info('[GitHub Webhook] Ping event received successfully.');
            return response()->json(['status' => 'success', 'message' => 'Pong! Webhook connected successfully.']);
        }

        if ($event !== 'push') {
            return response()->json(['status' => 'ignored', 'message' => "Event '{$event}' ignored."], 200);
        }

        // 3. Check Branch (Deploy on 'main' branch)
        $rawContent = $request->getContent();
        $payload = json_decode($rawContent, true) ?: [];
        if (empty($payload)) {
            $rawPayload = $request->input('payload');
            if (is_string($rawPayload)) {
                $payload = json_decode($rawPayload, true) ?: [];
            } else {
                $payload = $request->all();
            }
        }

        $ref = $payload['ref'] ?? $request->input('ref') ?? $request->json('ref');
        $targetBranch = 'refs/heads/main';
        
        if ($ref !== $targetBranch) {
            return response()->json([
                'status' => 'ignored',
                'message' => "Push to branch '{$ref}' ignored (only '{$targetBranch}' is deployed)."
            ], 200);
        }

        // 4. Trigger Deployment in Background (Instant 200 OK Response)
        $basePath = base_path();
        $commitHash = $payload['after'] ?? $request->input('after') ?? 'unknown';
        $pusherName = $payload['pusher']['name'] ?? $request->input('pusher.name') ?? 'unknown';
        Log::info("[GitHub Webhook] Push event verified for commit {$commitHash} by {$pusherName}. Launching background deployment.");

        // Asynchronous detached background execution (resumes in 5ms without hanging GitHub)
        $phpBinary = is_file('C:\\php\\php.exe') ? 'C:\\php\\php.exe' : 'php';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B {$phpBinary} \"{$basePath}\\artisan\" deploy:run \"{$commitHash}\" > NUL 2>&1", "r"));
        } else {
            exec("{$phpBinary} \"{$basePath}/artisan\" deploy:run \"{$commitHash}\" > /dev/null 2>&1 &");
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Deployment queued and executing in background.',
            'commit' => $commitHash,
            'pusher' => $pusherName,
        ], 200);
    }
}
