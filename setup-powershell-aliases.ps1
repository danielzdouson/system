# SACCO System PowerShell Aliases
# Run this once in PowerShell as Administrator to add aliases to your profile

Write-Host "Setting up SACCO System PowerShell aliases..." -ForegroundColor Green

# Get PowerShell profile path
$profilePath = $PROFILE.CurrentUserAllHosts

# Create profile directory if it doesn't exist
$profileDir = Split-Path $profilePath -Parent
if (!(Test-Path $profileDir)) {
    New-Item -ItemType Directory -Path $profileDir -Force
}

# Add aliases to profile
$aliases = @"

# SACCO System Aliases
function sacco-start {
    Write-Host "🚀 Starting SACCO System..." -ForegroundColor Yellow
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    wsl -d Ubuntu bash -c "cd /home/danielz/projects/system && docker-compose down && docker-compose up -d --force-recreate && sleep 30 && docker exec saco_system_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && docker exec saco_system_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache && echo '✅ SACCO System started successfully!'"
    Write-Host "✅ SACCO System startup complete!" -ForegroundColor Green
}

function sacco-stop {
    Write-Host "🛑 Stopping SACCO System..." -ForegroundColor Yellow
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    wsl -d Ubuntu bash -c "cd /home/danielz/projects/system && docker-compose down && echo '🛑 SACCO System stopped'"
    Write-Host "🛑 SACCO System stopped!" -ForegroundColor Red
}

function sacco-restart {
    Write-Host "🔄 Restarting SACCO System..." -ForegroundColor Yellow
    sacco-stop
    Start-Sleep -Seconds 2
    sacco-start
}

function sacco-status {
    Write-Host "📊 SACCO System Status:" -ForegroundColor Cyan
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    wsl -d Ubuntu bash -c "cd /home/danielz/projects/system && docker-compose ps"
}

function sacco-logs {
    Write-Host "📋 SACCO System Logs:" -ForegroundColor Cyan
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    wsl -d Ubuntu bash -c "cd /home/danielz/projects/system && docker logs saco_system_php --tail 20"
}

function sacco-test {
    Write-Host "🧪 Testing SACCO System..." -ForegroundColor Yellow
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8080" -Method Head -TimeoutSec 10
        if ($response.StatusCode -eq 200) {
            Write-Host "✅ SACCO System is working!" -ForegroundColor Green
        } else {
            Write-Host "❌ SACCO System has issues (Status: $($response.StatusCode))" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ SACCO System is not responding" -ForegroundColor Red
    }
}

function sacco {
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    Write-Host "📁 Now in SACCO System directory" -ForegroundColor Green
}

function sacco-dir {
    Set-Location "\\wsl$\Ubuntu\home\danielz\projects\system"
    Write-Host "📁 Now in SACCO System directory" -ForegroundColor Green
}

Write-Host "SACCO System aliases loaded!" -ForegroundColor Green
"@

# Add to profile if not already there
if (Test-Path $profilePath) {
    $existingContent = Get-Content $profilePath -Raw
    if ($existingContent -notmatch "# SACCO System Aliases") {
        Add-Content -Path $profilePath -Value $aliases
    }
} else {
    Set-Content -Path $profilePath -Value $aliases
}

Write-Host "✅ PowerShell aliases added to: $profilePath" -ForegroundColor Green
Write-Host "🔄 Reload PowerShell or run: . `$PROFILE" -ForegroundColor Yellow
Write-Host ""
Write-Host "📋 Available commands:" -ForegroundColor Cyan
Write-Host "  sacco-start    - Start the system with permission fixes" -ForegroundColor White
Write-Host "  sacco-stop     - Stop all containers" -ForegroundColor White
Write-Host "  sacco-restart  - Stop and start the system" -ForegroundColor White
Write-Host "  sacco-status   - Check container status" -ForegroundColor White
Write-Host "  sacco-logs     - View recent logs" -ForegroundColor White
Write-Host "  sacco-test     - Test if system is working" -ForegroundColor White
Write-Host "  sacco          - Go to project directory" -ForegroundColor White
Write-Host "  sacco-dir      - Go to project directory" -ForegroundColor White
