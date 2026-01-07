$ErrorActionPreference = "Continue"
$ProgressPreference = "SilentlyContinue"

try {
    Write-Host "================================================" -ForegroundColor Cyan
    Write-Host " MIDDO CLEANUP - Robust Version" -ForegroundColor Cyan
    Write-Host "================================================" -ForegroundColor Cyan
    
    $count = 0
    $deleted = 0
    $patterns = @("*.backup*", "*.php.txt", "*.twig.txt", "*_fix", "*_corrupt*")
    
    foreach ($pattern in $patterns) {
        Write-Host "[SCAN] Pattern: $pattern" -ForegroundColor Yellow
        
        $files = Get-ChildItem -Path src,templates -Filter $pattern -Recurse -File -ErrorAction SilentlyContinue
        
        foreach ($file in $files) {
            $count++
            
            try {
                Remove-Item $file.FullName -Force -ErrorAction Stop
                $deleted++
                Write-Host "  [OK] $($file.Name)" -ForegroundColor Gray
            }
            catch {
                Write-Host "  [SKIP] $($file.Name)" -ForegroundColor Yellow
            }
            
            if ($count % 50 -eq 0) {
                Start-Sleep -Milliseconds 100
            }
        }
    }
    
    Write-Host ""
    Write-Host "[DONE] Deleted: $deleted / $count files" -ForegroundColor Green
}
catch {
    Write-Host "[ERROR] $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
