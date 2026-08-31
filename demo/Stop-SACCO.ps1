$ErrorActionPreference = "SilentlyContinue"

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$PidFile = Join-Path $Root ".sacco-demo-pid"
$PortFile = Join-Path $Root ".sacco-demo-port"

if (Test-Path $PidFile) {
    $Pid = Get-Content $PidFile
    if ($Pid -match "^\d+$") {
        Stop-Process -Id ([int]$Pid) -Force
    }
}

Remove-Item $PidFile, $PortFile -Force
Write-Host "SACCO Demo stopped." -ForegroundColor Green
Read-Host "Press Enter to close"
