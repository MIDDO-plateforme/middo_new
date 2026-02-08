# ============================================
# MIDDO GENIUS PRO - Sauvegarde Automatique
# Version 1.1 - Ultra-Securisee (CORRIGEE)
# Auteur: MIDDO Team
# Date: 16/11/2025
# ============================================

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - BACKUP SYSTEM   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration des chemins
$ProjectPath = "C:\Users\MBANE LOKOTA\middo_new"
$BackupPath = "C:\MIDDO-Backup"
$Timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

Write-Host "Chemin du projet: $ProjectPath" -ForegroundColor Yellow
Write-Host "Chemin de sauvegarde: $BackupPath" -ForegroundColor Yellow
Write-Host ""

# Verification que le projet existe
if (Test-Path $ProjectPath) {
    Write-Host "[OK] Projet MIDDO trouve!" -ForegroundColor Green
} else {
    Write-Host "[ERREUR] Projet MIDDO non trouve!" -ForegroundColor Red
    Write-Host "Verifie le chemin: $ProjectPath" -ForegroundColor Red
    pause
    exit
}

# Creation du dossier de backup
Write-Host "Creation du dossier de sauvegarde..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path $BackupPath | Out-Null
Write-Host "[OK] Dossier cree!" -ForegroundColor Green
Write-Host ""

# Sauvegarde complete
$BackupFolder = "$BackupPath\Session-$Timestamp"
Write-Host "Copie des fichiers en cours..." -ForegroundColor Yellow
Write-Host "Cela peut prendre quelques minutes..." -ForegroundColor Yellow

try {
    Copy-Item -Path $ProjectPath -Destination $BackupFolder -Recurse -Force -ErrorAction Stop
    Write-Host "[OK] Copie terminee avec succes!" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Erreur lors de la copie: $_" -ForegroundColor Red
    pause
    exit
}

Write-Host ""
Write-Host "Emplacement: $BackupFolder" -ForegroundColor Cyan
Write-Host ""

# Compression en ZIP
Write-Host "Compression de l'archive en cours..." -ForegroundColor Yellow
try {
    Compress-Archive -Path $BackupFolder -DestinationPath "$BackupFolder.zip" -Force -ErrorAction Stop
    Write-Host "[OK] Archive creee avec succes!" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Erreur lors de la compression: $_" -ForegroundColor Red
    pause
    exit
}

Write-Host ""
Write-Host "Fichier archive: $BackupFolder.zip" -ForegroundColor Green
Write-Host ""

# Calcul de la taille
$ArchiveSize = (Get-Item "$BackupFolder.zip").Length / 1MB
$ArchiveSizeRounded = [math]::Round($ArchiveSize, 2)
Write-Host "Taille de l'archive: $ArchiveSizeRounded MB" -ForegroundColor Cyan
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   SAUVEGARDE TERMINEE AVEC SUCCES   " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Ton projet MIDDO est maintenant sauvegarde!" -ForegroundColor Yellow
Write-Host "Archive ZIP: $BackupFolder.zip" -ForegroundColor Yellow
Write-Host ""

pause
