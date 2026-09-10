Set-Location "c:\Users\HP\Documents\GitHub\laravel-emprecord"
Write-Host "[Auto-Deploy Watcher] Monitoring GitHub for changes..."

while ($true) {
    try {
        git fetch origin main 2>$null
        $LOCAL = (git rev-parse HEAD 2>$null)
        $REMOTE = (git rev-parse origin/main 2>$null)

        if ($LOCAL -and $REMOTE -and ($LOCAL.Trim() -ne $REMOTE.Trim())) {
            $time = Get-Date -Format "HH:mm:ss"
            Write-Host "[$time] New commit detected! Pulling changes..."
            
            git pull origin main
            
            Write-Host "[$time] Running migrations in Docker..."
            docker compose exec -T app php artisan migrate --force 2>$null
            
            Write-Host "[$time] Clearing cache in Docker..."
            docker compose exec -T app php artisan optimize:clear 2>$null
            
            Write-Host "[$time] Successfully deployed latest commit!"
        }
    }
    catch {
        $error.Clear()
    }

    Start-Sleep -Seconds 3
}
