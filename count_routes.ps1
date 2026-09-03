$content = Get-Content "app/Config/Routes.php" -Raw
$matches = [regex]::Matches($content, '\$routes->([a-zA-Z]+)\s\(')
$methods = @()
foreach ($m in $matches) { $methods += $m.Groups[1].Value }

$byType = @{}
foreach ($m in $methods) {
    $ml = $m.ToLower()
    if (-not $byType.ContainsKey($ml)) { $byType[$ml] = 0 }
    $byType[$ml]++
}

Write-Host "Method counts:"
$byType.GetEnumerator() | ForEach-Object { Write-Host "  $($_.Key): $($_.Value)" }
Write-Host "Total route definitions: $($methods.Count)"

# Now list all route URIs
$uriMatches = [regex]::Matches($content, '\$routes->(?:match|get|post|put|patch|delete)\s*\(\s*[""]([^"""]+)[""]')
foreach ($u in $uriMatches) {
    $method = $u.Groups[1].Value
    $uri = $u.Groups[2].Value
    Write-Host "$($method.Value): $($uri.Value)"
}