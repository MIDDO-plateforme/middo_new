# ============================================
# MIDDO GENIUS PRO - Restauration Automatique
# Version 1.0
# ============================================

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - RESTORE SYSTEM   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$BackupPath = "C:\MIDDO-Backup"
$RestorePath = "C:\MIDDO-Restored"

Write-Host "Recherche des sauvegardes disponibles..." -ForegroundColor Yellow
Write-Host ""

$Backups = Get-ChildItem -Path $BackupPath -Filter "*.zip" | Sort-Object LastWriteTime -Descending

if ($Backups.Count -eq 0) {
    Write-Host "[ERREUR] Aucune sauvegarde trouvee!" -ForegroundColor Red
    pause
    exit
}

Write-Host "Sauvegardes disponibles:" -ForegroundColor Green
Write-Host ""

for ($i = 0; $i -lt $Backups.Count; $i++) {
    $backup = $Backups[$i]
    $size = [math]::Round($backup.Length / 1MB, 2)
    Write-Host "[$i] $($backup.Name) - $size MB - $($backup.LastWriteTime)" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "Entrez le numero de la sauvegarde a restaurer (0-$($Backups.Count - 1)):" -ForegroundColor Yellow
$choice = Read-Host "Numero"

if ($choice -match '^\d+$' -and [int]$choice -ge 0 -and [int]$choice -lt $Backups.Count) {
    $selectedBackup = $Backups[[int]$choice]
    
    Write-Host ""
    Write-Host "Restauration de: $($selectedBackup.Name)" -ForegroundColor Yellow
    Write-Host ""
    
    if (Test-Path $RestorePath) {
        Remove-Item -Path $RestorePath -Recurse -Force
    }
    
    New-Item -ItemType Directory -Force -Path $RestorePath | Out-Null
    
    Write-Host "Extraction de l archive en cours..." -ForegroundColor Yellow
    
    try {
        Expand-Archive -Path $selectedBackup.FullName -DestinationPath $RestorePath -Force
        Write-Host "[OK] Extraction reussie!" -ForegroundColor Green
    } catch {
        Write-Host "[ERREUR] Erreur lors de l extraction" -ForegroundColor Red
        pause
        exit
    }
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "   RESTAURATION TERMINEE AVEC SUCCES   " -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Ton projet MIDDO a ete restaure dans:" -ForegroundColor Yellow
    Write-Host "$RestorePath" -ForegroundColor Cyan
    Write-Host ""
    
} else {
    Write-Host "[ERREUR] Numero invalide!" -ForegroundColor Red
}

pause
