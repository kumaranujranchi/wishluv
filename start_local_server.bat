@echo off
echo ========================================
echo  Wishluv Buildcon - Local Server Setup
echo ========================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP or add it to your system PATH
    echo.
    echo Download PHP from: https://www.php.net/downloads
    echo Or install XAMPP from: https://www.apachefriends.org/
    pause
    exit /b 1
)

echo PHP is installed:
php --version
echo.

REM Check if we're in the right directory
if not exist "index.php" (
    echo ERROR: index.php not found
    echo Please run this script from the project root directory
    pause
    exit /b 1
)

echo Starting PHP development server...
echo.
echo Website will be available at:
echo   http://localhost:8000
echo.
echo Admin Panel will be available at:
echo   http://localhost:8000/admin
echo.
echo Setup Test will be available at:
echo   http://localhost:8000/test_local_setup.php
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

REM Start PHP built-in server
php -S localhost:8000

pause
