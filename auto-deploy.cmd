@echo off
title Laravel Auto-Deploy Watcher
powershell -ExecutionPolicy Bypass -File "%~dp0auto-deploy.ps1"
