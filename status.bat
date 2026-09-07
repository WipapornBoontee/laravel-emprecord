@echo off
title Check Server Status
color 0B
echo ===================================================
echo             SYSTEM HEALTH CHECK STATUS
echo ===================================================
echo.

:: 1. Check PHP Server (Port 8005)
echo [1] Checking Laravel Server (Port 8005)...
netstat -ano | findstr :8005 | findstr LISTENING > nul
if %errorlevel% equ 0 (
    echo     Status: [ RUNNING ] (Port 8005 is active)
) else (
    echo     Status: [ STOPPED ] (Laravel server is not running)
)
echo.

:: 2. Check Docker Containers
echo [2] Checking Docker Containers...
docker ps --format "table   - {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo.

:: 3. Test HTTP Response
echo [3] Testing HTTP Response (http://localhost:8005)...
curl -I -s http://localhost:8005 | findstr /i "HTTP"
echo.

echo ===================================================
echo   Press any key to close this status window...
echo ===================================================
pause > nul
