Write-Host "[Auto-Deploy Watcher] Started. Monitoring GitHub for new commits..."

Set-Location "c:\Users\HP\Documents\GitHub\laravel-emprecord"

while ($true) {
    try {
        git fetch origin main 2>$null
        $LOCAL = (git rev-parse HEAD 2>$null)
        $REMOTE = (git rev-parse origin/main 2>$null)

        if ($LOCAL -and $REMOTE -and ($LOCAL.Trim() -ne $REMOTE.Trim())) {
            $time = Get-Date -Format "HH:mm:ss"
            Write-Host "[$time] New commit detected! Deploying..."
            
            git pull origin main
            & "C:\php\php.exe" artisan migrate --force
            & "C:\php\php.exe" artisan optimize:clear
            
            Write-Host "[$time] Deployment completed successfully!"
        }
    }
    catch {
        $error.Clear()
    }

    Start-Sleep -Seconds 5
}
