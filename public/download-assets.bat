@echo off
echo ============================================
echo   Tabler UI Asset Downloader
echo   Slim Framework + Tabler Integration
echo ============================================
echo.

REM Check if PHP is available
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP not found in PATH!
    echo Please make sure PHP is installed and added to your PATH.
    echo.
    echo If you have PHP installed, try running:
    echo   php %~dp0download-tabler.php
    echo.
    pause
    exit /b 1
)

REM Run the download script
echo Downloading Tabler UI assets...
echo.
php "%~dp0download-tabler.php"

if %errorlevel% neq 0 (
    echo.
    echo ERROR: Download failed! Check the error messages above.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Download complete!
echo.
echo   Next steps:
echo   1. Start the PHP dev server:
echo      php -S localhost:8080 -t public/
echo.
echo   2. Open http://localhost:8080/dashboard
echo      in your browser.
echo ============================================
echo.
pause
