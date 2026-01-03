# 🔄 MIDDO GENIUS PRO - Restauration Automatique
# Script de restauration en cas de bug ou problème

param(
    [string]$BackupPath = "C:\MIDDO-Backup",
    [string]$RestorePath = "C:\MIDDO-Restored"
)

Write-Host "🔄 Restauration MIDDO en cours..." -ForegroundColor Magenta
Write-Host "================================" -ForegroundColor Magenta

# Recherche de la dernière sauvegarde
$LastBackup = Get-ChildItem "$BackupPath\*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 1

if ($LastBackup) {
    Write-Host "📦 Sauvegarde trouvée : $($LastBackup.Name)" -ForegroundColor Yellow
    
    # Création du dossier de restauration
    New-Item -ItemType Directory -Force -Path $RestorePath
    
    # Extraction de l'archive
    try {
        Expand-Archive -Path $LastBackup.FullName -DestinationPath $RestorePath -Force
        Write-Host "✅ Restauration terminée dans : $RestorePath" -ForegroundColor Green
        Write-Host "🎉 Ton projet MIDDO est restauré !" -ForegroundColor Green
    } catch {
        Write-Host "❌ Erreur lors de la restauration : $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Aucune sauvegarde trouvée dans : $BackupPath" -ForegroundColor Red
    Write-Host "💡 Assure-toi d'avoir lancé le script de setup d'abord" -ForegroundColor Yellow
}

Write-Host "📍 Vérife le dossier : $RestorePath" -ForegroundColor Cyan