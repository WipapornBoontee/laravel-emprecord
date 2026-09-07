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
        $ref = $request->input('ref');
        $targetBranch = 'refs/heads/main';
        
        if ($ref !== $targetBranch) {
            return response()->json([
                'status' => 'ignored',
                'message' => "Push to branch '{$ref}' ignored (only '{$targetBranch}' is deployed)."
            ], 200);
        }

        // 4. Execute Deployment Commands
        $basePath = base_path();
        Log::info("[GitHub Webhook] Starting Auto-Deployment for commit: " . $request->input('after'));

        $outputLog = [];

        try {
            // Command 1: git pull origin main
            $gitProcess = Process::fromShellCommandline('git pull origin main', $basePath);
            $gitProcess->setTimeout(120);
            $gitProcess->run();
            $outputLog['git_pull'] = $gitProcess->getOutput() . $gitProcess->getErrorOutput();

            // Command 2: php artisan migrate --force
            $migrateProcess = Process::fromShellCommandline('php artisan migrate --force', $basePath);
            $migrateProcess->setTimeout(60);
            $migrateProcess->run();
            $outputLog['migrate'] = $migrateProcess->getOutput() . $migrateProcess->getErrorOutput();

            // Command 3: php artisan optimize:clear
            $optimizeProcess = Process::fromShellCommandline('php artisan optimize:clear', $basePath);
            $optimizeProcess->setTimeout(30);
            $optimizeProcess->run();
            $outputLog['optimize_clear'] = $optimizeProcess->getOutput() . $optimizeProcess->getErrorOutput();

            Log::info('[GitHub Webhook] Deployment finished.', $outputLog);

            return response()->json([
                'status' => 'success',
                'message' => 'Auto-deployed successfully!',
                'commit' => $request->input('after'),
                'pusher' => $request->input('pusher.name'),
                'output' => $outputLog,
            ], 200);

        } catch (\Exception $e) {
            Log::error('[GitHub Webhook] Deployment error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Deployment failed: ' . $e->getMessage(),
                'output' => $outputLog,
            ], 500);
        }
    }
}
