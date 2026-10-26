@echo off
setlocal enabledelayedexpansion

:: Set console encoding to UTF-8
chcp 65001 >nul

cd /d "%~dp0"

echo ========================================================
echo   Tamil Kalolsavam CI4 - GitHub Direct Push Script
echo   Repository: https://github.com/dsavio83/Tamil_kalolsavam_CI4.git
echo ========================================================
echo.

:: Ensure Git user info is configured
git config user.name "dsavio83"
git config user.email "dsavio83@gmail.com"

:: Verify remote origin
git remote get-url origin >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [*] Adding remote origin...
    git remote add origin https://github.com/dsavio83/Tamil_kalolsavam_CI4.git
)

echo [*] Checking current status...
git status -s
echo.

:: Prompt for commit message
set "msg="
set /p "msg=Enter commit message (Press Enter for default timestamp message): "

if "!msg!"=="" (
    for /f "tokens=1-4 delims=/.- " %%a in ("%date%") do set d=%%a-%%b-%%c
    set "msg=Update: %date% %time%"
)

echo.
echo [*] Adding all changes...
git add -A

echo [*] Committing changes...
git commit -m "!msg!"

echo.
echo [*] Pushing to GitHub (origin/main)...
git push origin main

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================================
    echo   [SUCCESS] GitHub-il kooppugal vetrigaramaga update seiyappattana!
    echo   Repository up to date: https://github.com/dsavio83/Tamil_kalolsavam_CI4
    echo ========================================================
) else (
    echo.
    echo ========================================================
    echo   [ERROR] Push seivadhil siramam erpattadhu. Melulla thagavalai parka.
    echo ========================================================
)

echo.
pause
