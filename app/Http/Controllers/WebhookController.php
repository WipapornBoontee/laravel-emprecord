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

        // 4. Execute Deployment Commands
        $basePath = base_path();
        $commitHash = $payload['after'] ?? $request->input('after') ?? 'unknown';
        Log::info("[GitHub Webhook] Starting Auto-Deployment for commit: {$commitHash}");

        // Locate binaries robustly on Windows / Linux
        $gitBinary = is_file('C:\\Program Files\\Git\\cmd\\git.exe') ? '"C:\\Program Files\\Git\\cmd\\git.exe"' : 'git';
        $phpBinary = defined('PHP_BINARY') && is_file(PHP_BINARY) ? '"' . PHP_BINARY . '"' : (is_file('C:\\php\\php.exe') ? '"C:\\php\\php.exe"' : 'php');

        $env = array_merge($_SERVER, $_ENV, [
            'PATH' => 'C:\\php;C:\\Program Files\\Git\\cmd;' . (getenv('PATH') ?: '') . ';' . (isset($_SERVER['PATH']) ? $_SERVER['PATH'] : ''),
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'USERPROFILE' => getenv('USERPROFILE') ?: 'C:\\Users\\HP',
            'HOMEDRIVE' => getenv('HOMEDRIVE') ?: 'C:',
            'HOMEPATH' => getenv('HOMEPATH') ?: '\\Users\\HP',
            'LOCALAPPDATA' => getenv('LOCALAPPDATA') ?: 'C:\\Users\\HP\\AppData\\Local',
            'APPDATA' => getenv('APPDATA') ?: 'C:\\Users\\HP\\AppData\\Roaming',
            'GIT_TERMINAL_PROMPT' => '0',
            'GCM_INTERACTIVE' => 'never',
        ]);

        $outputLog = [];

        try {
            // Command 1: git pull origin main (support GITHUB_TOKEN for private repos)
            $githubToken = env('GITHUB_TOKEN');
            $pullTarget = $githubToken 
                ? "https://{$githubToken}@github.com/WipapornBoontee/laravel-emprecord.git main" 
                : "origin main";

            $gitProcess = Process::fromShellCommandline("{$gitBinary} pull {$pullTarget}", $basePath, $env);
            $gitProcess->setTimeout(30);
            $gitProcess->run();
            $outputLog['git_pull'] = trim($gitProcess->getOutput() . $gitProcess->getErrorOutput());

            // Command 2: php artisan migrate --force
            $migrateProcess = Process::fromShellCommandline("{$phpBinary} artisan migrate --force", $basePath, $env);
            $migrateProcess->setTimeout(30);
            $migrateProcess->run();
            $outputLog['migrate'] = trim($migrateProcess->getOutput() . $migrateProcess->getErrorOutput());

            // Command 3: php artisan optimize:clear
            $optimizeProcess = Process::fromShellCommandline("{$phpBinary} artisan optimize:clear", $basePath, $env);
            $optimizeProcess->setTimeout(10);
            $optimizeProcess->run();
            $outputLog['optimize_clear'] = trim($optimizeProcess->getOutput() . $optimizeProcess->getErrorOutput());

            Log::info('[GitHub Webhook] Deployment finished successfully.', $outputLog);

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
