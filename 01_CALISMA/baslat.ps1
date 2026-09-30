$ErrorActionPreference = "Stop"
$php = "C:\tools\php85\php.exe"
$mariaBin = "C:\tools\mariadb-10.11.19-winx64\bin"
$ini = "C:\tools\mariadb-10.11.19-winx64\my.ini"
$site = Join-Path $PSScriptRoot "public_html"
$sql = Join-Path $PSScriptRoot "veritabani\ailevenesilakade_vt.sql"
$override = Join-Path $PSScriptRoot "veritabani\local-overrides.sql"
$marker = Join-Path $PSScriptRoot "veritabani\.imported"

if (-not (Get-Process mariadbd -ErrorAction SilentlyContinue)) {
    Start-Process -FilePath (Join-Path $mariaBin "mariadbd.exe") -ArgumentList "--defaults-file=$ini","--console" -WindowStyle Hidden
    Start-Sleep -Seconds 4
}

$mysql = Join-Path $mariaBin "mysql.exe"
if (-not (Test-Path $marker)) {
    & $mysql -u root -e "CREATE DATABASE IF NOT EXISTS ailevenesilakade_vt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    cmd /c "`"$mysql`" -u root ailevenesilakade_vt < `"$sql`""
    if ($LASTEXITCODE -ne 0) { throw "SQL ice aktarma basarisiz" }
    cmd /c "`"$mysql`" -u root ailevenesilakade_vt < `"$override`""
    if ($LASTEXITCODE -ne 0) { throw "Yerel ayar SQL basarisiz" }
    Set-Content -Path $marker -Value (Get-Date -Format o)
}

$env:DB_HOST = "127.0.0.1"
$env:DB_NAME = "ailevenesilakade_vt"
$env:DB_USER = "root"
$env:DB_PASS = ""
Set-Location $site
Write-Host "Site: http://127.0.0.1:8080/"
& $php -S 127.0.0.1:8080 router.php
