@echo off
title Laravel Server - Hybrid Mode
echo ===================================================
echo   Starting Laravel Hybrid High-Speed Environment...
echo ===================================================

:: 1. Start Docker containers (MySQL, phpMyAdmin, Ngrok, Cloudflared) in background
echo [*] Starting Docker Services...
docker compose up -d

:: 2. Start native Laravel server on Windows (High-Performance SSD)
echo [*] Starting Laravel Server on port 8005...
start "Laravel Server (Port 8005)" /min "C:\php\php.exe" artisan serve --host=0.0.0.0 --port=8005

echo.
echo ===================================================
echo   [OK] All Services are RUNNING!
echo   - Web App:     http://localhost:8005
echo   - phpMyAdmin:  http://localhost:8090
echo ===================================================
timeout /t 3 > nul
exit
