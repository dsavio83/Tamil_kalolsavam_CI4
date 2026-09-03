$env:PREFEST_E2E_USERNAME = 'itadmin'
$env:PREFEST_E2E_PASSWORD = 'admin'
$env:PREFEST_E2E_BASE_URL = 'http://localhost/tk25'

# Verify credentials are set
Write-Host "Username: $env:PREFEST_E2E_USERNAME"
Write-Host "Password: [REDACTED]"
Write-Host "Base URL: $env:PREFEST_E2E_BASE_URL"

# Run the full E2E verification using Playwright with system Chrome
Write-Host "`nStarting Pre Fest Runtime E2E verification..." `n"

# Launch Chrome via Playwright with explicit executablePath
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"
if (-not (Test-Path $chromePath)) {
    Write-Host "ERROR: Chrome executable not found at $chromePath"
    exit 1
}
Write-Host "Chrome available: $true"

# Begin verification using node Playwright script
Write-Host "Launching browser via Playwright..."