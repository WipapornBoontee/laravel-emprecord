<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tunnel:sync', function () {
    $this->info('[*] Syncing Cloudflare Tunnel with Worker...');
    sleep(2); // Wait a moment for container logs
    $logs = shell_exec('docker logs laravel_emp_cf 2>&1');
    if ($logs && preg_match('/https:\/\/([a-zA-Z0-9\-]+\.trycloudflare\.com)/', $logs, $matches)) {
        $target = $matches[1];
        $this->line("    Active Tunnel: <info>{$target}</info>");
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(5)->post('https://round-mode-5c41.wibo00101.workers.dev/__sync_tunnel', [
                'key' => 'wibo_secret_key_2026',
                'target' => $target,
            ]);
            if ($response->successful()) {
                $this->info('    [OK] Cloudflare Worker Synced Successfully!');
            } else {
                $this->warn('    [!] Worker sync returned status: ' . $response->status());
            }
        } catch (\Throwable $e) {
            $this->warn('    [!] Sync notice: ' . $e->getMessage());
        }
    } else {
        $this->warn('    [!] No active trycloudflare.com URL found in container logs.');
    }
})->purpose('Sync dynamic Cloudflare tunnel domain to worker');

Artisan::command('deploy:run {commit=latest}', function ($commit) {
    $this->info("[*] Starting deployment for commit: {$commit}");
    Log::info("[Auto-Deploy] Starting background deployment for commit: {$commit}");
    $basePath = base_path();
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
        $githubToken = env('GITHUB_TOKEN');
        $pullTarget = $githubToken 
            ? "https://{$githubToken}@github.com/WipapornBoontee/laravel-emprecord.git main" 
            : "origin main";

        $gitProcess = Symfony\Component\Process\Process::fromShellCommandline("{$gitBinary} pull {$pullTarget}", $basePath, $env);
        $gitProcess->setTimeout(60);
        $gitProcess->run();
        $outputLog['git_pull'] = trim($gitProcess->getOutput() . $gitProcess->getErrorOutput());

        $migrateProcess = Symfony\Component\Process\Process::fromShellCommandline("{$phpBinary} artisan migrate --force", $basePath, $env);
        $migrateProcess->setTimeout(60);
        $migrateProcess->run();
        $outputLog['migrate'] = trim($migrateProcess->getOutput() . $migrateProcess->getErrorOutput());

        $optimizeProcess = Symfony\Component\Process\Process::fromShellCommandline("{$phpBinary} artisan optimize:clear", $basePath, $env);
        $optimizeProcess->setTimeout(30);
        $optimizeProcess->run();
        $outputLog['optimize_clear'] = trim($optimizeProcess->getOutput() . $optimizeProcess->getErrorOutput());

        Log::info('[Auto-Deploy] Deployment finished successfully.', $outputLog);
        $this->info('[OK] Deployment finished successfully.');
    } catch (\Throwable $e) {
        Log::error('[Auto-Deploy] Deployment error: ' . $e->getMessage());
        $this->error('[!] Deployment error: ' . $e->getMessage());
    }
})->purpose('Run git pull, migration, and cache clear in background');
