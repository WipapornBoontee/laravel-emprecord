@echo off
title Laravel Server - Hybrid Mode
echo ===================================================
echo   Starting Laravel Hybrid High-Speed Environment...
echo ===================================================

:: 1. Start Docker containers (MySQL, phpMyAdmin, Ngrok, Cloudflared) in background
echo [*] Starting Docker Services...
docker compose up -d

:: 2. Wait 3 seconds for Cloudflare tunnel to initialize
echo [*] Waiting for Cloudflare Tunnel to initialize...
timeout /t 3 > nul

:: 3. Extract Tunnel URL and Auto-Sync to Cloudflare Worker
echo [*] Syncing Tunnel URL with Cloudflare Worker...
powershell -Command "$log = docker logs laravel_emp_cf 2>&1 | Select-String 'https://.*\.trycloudflare\.com' | Select-Object -Last 1; if ($log -match 'https://([a-zA-Z0-9\-]+\.trycloudflare\.com)') { $target = $matches[1]; Write-Host '    Active Tunnel:' $target; try { $res = Invoke-RestMethod -Uri 'https://round-mode-5c41.wibo00101.workers.dev/__sync_tunnel' -Method POST -Body (@{key='wibo_secret_key_2026'; target=$target} | ConvertTo-Json) -ContentType 'application/json' -TimeoutSec 5; Write-Host '    [OK] Worker Synced Successfully!' -ForegroundColor Green } catch { Write-Host '    [!] Sync notice:' $_.Exception.Message } }"

:: 4. Start native Laravel server invisibly in the background (No CMD Window)
echo [*] Starting Laravel Server in background...
powershell -Command "Start-Process 'C:\php\php.exe' -ArgumentList 'artisan serve --host=0.0.0.0 --port=8005 --no-reload' -WindowStyle Hidden"

echo.
echo ===================================================
echo   [OK] All Services are RUNNING in Background!
echo   - Public Web:  https://round-mode-5c41.wibo00101.workers.dev
echo   - Local Web:   http://localhost:8005
echo   - phpMyAdmin:  http://localhost:8090
echo ===================================================
timeout /t 3 > nul
exit
