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
