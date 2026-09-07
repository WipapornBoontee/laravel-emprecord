@echo off
title Stop Laravel Server
echo ===================================================
echo   Stopping All Services...
echo ===================================================

:: 1. Stop Docker Containers
echo [*] Stopping Docker containers...
docker compose down

:: 2. Stop native PHP artisan server process on port 8005
echo [*] Stopping PHP server...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8005"') do (
    taskkill /f /pid %%a > nul 2>&1
)

echo.
echo ===================================================
echo   [OK] All Services have been STOPPED.
echo ===================================================
timeout /t 2 > nul
exit
