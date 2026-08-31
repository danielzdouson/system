[CmdletBinding()]
param(
    [string]$OutputDirectory = (Join-Path (Split-Path -Parent $MyInvocation.MyCommand.Path) "dist"),
    [string]$PhpRuntimeUrl = "https://windows.php.net/downloads/releases/latest/php-8.3-nts-Win32-vs16-x64-latest.zip",
    [switch]$SkipBuild
)

$ErrorActionPreference = "Stop"
$RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$BuildRoot = Join-Path $RepoRoot ".demo-build"
$PackageRoot = Join-Path $BuildRoot "SACCO-Demo"
$RuntimeZip = Join-Path $BuildRoot "php-runtime.zip"
$PhpCache = Join-Path $RepoRoot ".demo-cache"
$KeyBytes = New-Object byte[] 32
[Security.Cryptography.RandomNumberGenerator]::Fill($KeyBytes)
$DemoAppKey = "base64:" + [Convert]::ToBase64String($KeyBytes)

function Require-Command([string]$Name) {
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Required command '$Name' was not found. Install it on the development PC and try again."
    }
}

function Invoke-Checked([string]$FilePath, [string[]]$Arguments) {
    & $FilePath @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "'$FilePath $($Arguments -join ' ')' failed with exit code $LASTEXITCODE."
    }
}

Require-Command "docker"
Require-Command "npm"

if (Test-Path $BuildRoot) {
    Remove-Item $BuildRoot -Recurse -Force
}
New-Item -ItemType Directory -Force $PackageRoot, $PhpCache | Out-Null

if (-not $SkipBuild) {
    Write-Host "Building frontend assets..."
    Invoke-Checked "npm" @("ci")
    Invoke-Checked "npm" @("run", "build")
}

Write-Host "Building the PHP dependency set with Docker..."
Invoke-Checked "docker" @("compose", "build")
Invoke-Checked "docker" @(
    "compose", "run", "--rm", "--no-deps",
    "-e", "APP_ENV=local",
    "-e", "APP_DEBUG=false",
    "-e", "APP_KEY=$DemoAppKey",
    "-e", "DB_CONNECTION=sqlite",
    "-e", "DB_DATABASE=/var/www/html/database/database.sqlite",
    "-e", "CACHE_STORE=file",
    "-e", "SESSION_DRIVER=file",
    "-e", "QUEUE_CONNECTION=sync",
    "php", "composer", "install", "--no-dev", "--prefer-dist", "--no-interaction", "--optimize-autoloader"
)

New-Item -ItemType File -Force (Join-Path $RepoRoot "database\database.sqlite") | Out-Null
Write-Host "Creating a fresh SQLite demo database..."
Invoke-Checked "docker" @(
    "compose", "run", "--rm", "--no-deps",
    "-e", "APP_ENV=local",
    "-e", "APP_DEBUG=false",
    "-e", "APP_KEY=$DemoAppKey",
    "-e", "DB_CONNECTION=sqlite",
    "-e", "DB_DATABASE=/var/www/html/database/database.sqlite",
    "-e", "CACHE_STORE=file",
    "-e", "SESSION_DRIVER=file",
    "-e", "QUEUE_CONNECTION=sync",
    "php", "php", "artisan", "migrate:fresh", "--seed", "--force"
)
Invoke-Checked "docker" @(
    "compose", "run", "--rm", "--no-deps",
    "-e", "APP_ENV=local",
    "-e", "APP_DEBUG=false",
    "-e", "APP_KEY=$DemoAppKey",
    "-e", "DB_CONNECTION=sqlite",
    "-e", "DB_DATABASE=/var/www/html/database/database.sqlite",
    "-e", "CACHE_STORE=file",
    "-e", "SESSION_DRIVER=file",
    "-e", "QUEUE_CONNECTION=sync",
    "php", "php", "artisan", "db:seed", "--class=PresentationDemoSeeder", "--force"
)

Write-Host "Downloading portable PHP runtime..."
if (-not (Test-Path $RuntimeZip)) {
    Invoke-WebRequest -Uri $PhpRuntimeUrl -OutFile $RuntimeZip
}
$RuntimeDirectory = Join-Path $BuildRoot "runtime"
Expand-Archive -Path $RuntimeZip -DestinationPath $RuntimeDirectory -Force

$PhpIni = Join-Path $RuntimeDirectory "php.ini"
Copy-Item (Join-Path $RuntimeDirectory "php.ini-production") $PhpIni
Add-Content -Path $PhpIni -Value @"

extension_dir = "ext"
extension=curl
extension=gd
extension=mbstring
extension=openssl
extension=pdo_sqlite
extension=fileinfo
extension=zip
date.timezone=UTC
memory_limit=256M
"@

Write-Host "Assembling a clean package..."
$RuntimeTarget = Join-Path $PackageRoot "runtime"
Copy-Item $RuntimeDirectory $RuntimeTarget -Recurse

$RuntimeFiles = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "storage", "vendor", "artisan", "composer.json")
foreach ($Item in $RuntimeFiles) {
    $Source = Join-Path $RepoRoot $Item
    if (Test-Path $Source) {
        Copy-Item $Source (Join-Path $PackageRoot $Item) -Recurse -Force
    }
}

$DemoEnvironment = @"
APP_NAME="SACCO Presentation Demo"
APP_ENV=local
APP_KEY=$DemoAppKey
APP_DEBUG=false
APP_URL=http://127.0.0.1:8080
LOG_CHANNEL=single
LOG_LEVEL=warning
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
MAIL_MAILER=log
VITE_APP_NAME="SACCO Presentation Demo"
"@
Set-Content -Path (Join-Path $PackageRoot ".env") -Value $DemoEnvironment -Encoding UTF8
Copy-Item (Join-Path $RepoRoot "demo\*") $PackageRoot -Recurse -Force

$StoragePublic = Join-Path $PackageRoot "storage\app\public"
$PublicStorage = Join-Path $PackageRoot "public\storage"
New-Item -ItemType Directory -Force $StoragePublic | Out-Null
if (-not (Test-Path $PublicStorage)) {
    New-Item -ItemType Junction -Path $PublicStorage -Target $StoragePublic | Out-Null
}

Remove-Item (Join-Path $PackageRoot "database\migrations") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "database\seeders") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "database\factories") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "resources\css") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "resources\js") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "storage\framework\testing") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "storage\framework\cache") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "storage\framework\sessions") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "storage\framework\views") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $PackageRoot "storage\logs\*") -Force -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Force `
    (Join-Path $PackageRoot "storage\framework\cache"), `
    (Join-Path $PackageRoot "storage\framework\sessions"), `
    (Join-Path $PackageRoot "storage\framework\views"), `
    (Join-Path $PackageRoot "storage\logs") | Out-Null

$ZipPath = Join-Path $OutputDirectory "SACCO-Demo.zip"
New-Item -ItemType Directory -Force $OutputDirectory | Out-Null
Remove-Item $ZipPath -Force -ErrorAction SilentlyContinue
Compress-Archive -Path (Get-ChildItem -Force $PackageRoot).FullName -DestinationPath $ZipPath -CompressionLevel Optimal

Write-Host ""
Write-Host "Created $ZipPath" -ForegroundColor Green
Write-Host "The recipient only needs Windows and PowerShell; no Docker, Node, PHP, Composer, Git, or database server is required."
