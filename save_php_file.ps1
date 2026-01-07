param(
    [string]$FilePath,
    [string]$Content
)

$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText($FilePath, $Content, $utf8NoBom)

Write-Host "[OK] Saved: $FilePath (UTF-8 no BOM)" -ForegroundColor Green
