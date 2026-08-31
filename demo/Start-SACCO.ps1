$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$Php = Join-Path $Root "runtime\php.exe"
$PortFile = Join-Path $Root ".sacco-demo-port"
$PidFile = Join-Path $Root ".sacco-demo-pid"
$LogFile = Join-Path $Root "storage\logs\demo-server.log"
$ErrorLogFile = Join-Path $Root "storage\logs\demo-server-error.log"

function Fail([string]$Message) {
    Write-Host ""
    Write-Host "SACCO Demo could not start:" -ForegroundColor Red
    Write-Host $Message -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to close"
    exit 1
}

foreach ($Path in @(
    (Join-Path $Root "artisan"),
    (Join-Path $Root "vendor\autoload.php"),
    (Join-Path $Root "database\database.sqlite"),
    (Join-Path $Root "public\build\manifest.json"),
    $Php
)) {
    if (-not (Test-Path $Path)) {
        Fail "Required file is missing: $Path"
    }
}

$ExistingProcess = $null
if (Test-Path $PidFile) {
    $ExistingPid = Get-Content $PidFile -ErrorAction SilentlyContinue
    if ($ExistingPid -match "^\d+$") {
        $ExistingProcess = Get-Process -Id ([int]$ExistingPid) -ErrorAction SilentlyContinue
    }
}
if ($ExistingProcess) {
    Write-Host "SACCO Demo is already running." -ForegroundColor Green
    if (Test-Path $PortFile) {
        Start-Process "http://127.0.0.1:$((Get-Content $PortFile))"
    }
    exit 0
}

$Port = 8080
while ($Port -le 8090) {
    $InUse = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
    if (-not $InUse) {
        break
    }
    $Port++
}
if ($Port -gt 8090) {
    Fail "Ports 8080 through 8090 are already in use. Close one application and try again."
}

New-Item -ItemType Directory -Force (Split-Path $LogFile -Parent) | Out-Null
Set-Content -Path $PortFile -Value $Port
Remove-Item $PidFile -Force -ErrorAction SilentlyContinue

$Process = Start-Process `
    -FilePath $Php `
    -WorkingDirectory $Root `
    -ArgumentList @("artisan", "serve", "--host=127.0.0.1", "--port=$Port") `
    -RedirectStandardOutput $LogFile `
    -RedirectStandardError $ErrorLogFile `
    -PassThru

Set-Content -Path $PidFile -Value $Process.Id
$Url = "http://127.0.0.1:$Port"
$Ready = $false
for ($Attempt = 0; $Attempt -lt 30; $Attempt++) {
    Start-Sleep -Milliseconds 500
    try {
        $Response = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 2
        if ($Response.StatusCode -ge 200 -and $Response.StatusCode -lt 500) {
            $Ready = $true
            break
        }
    } catch {
    }
}

if (-not $Ready) {
    Stop-Process -Id $Process.Id -Force -ErrorAction SilentlyContinue
    Remove-Item $PidFile, $PortFile -Force -ErrorAction SilentlyContinue
    Fail "The local server did not become ready. Review storage\logs\demo-server.log."
}

Write-Host "SACCO Demo is running at $Url" -ForegroundColor Green
Write-Host "To stop it, double-click Stop-SACCO.bat." -ForegroundColor Cyan
Start-Process $Url
