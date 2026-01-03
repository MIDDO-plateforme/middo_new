# =================================================================
# SCRIPT DE CORRECTION AUTOMATIQUE - show.html.twig
# =================================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "   CORRECTION ENCODAGE show.html.twig" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Chemins
$projectPath = "C:\Users\MBANE LOKOTA\middo_new"
$templatePath = "$projectPath\templates\project"
$fichierDestination = "$templatePath\show.html.twig"
$fichierDownload = "$env:USERPROFILE\Downloads\show.html.twig.corrected.txt"

# Etape 1 : Vérifier que le fichier téléchargé existe
Write-Host "[1/5] Verification du fichier corrige..." -ForegroundColor Yellow
if (!(Test-Path $fichierDownload)) {
    Write-Host "ERREUR : Fichier $fichierDownload introuvable !" -ForegroundColor Red
    Write-Host "Telecharge d'abord le fichier show.html.twig.corrected depuis ton navigateur." -ForegroundColor Red
    pause
    exit
}
Write-Host "  OK - Fichier trouve !" -ForegroundColor Green

# Etape 2 : Créer un backup de l'ancien fichier
Write-Host "[2/5] Sauvegarde de l'ancien fichier..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupPath = "$fichierDestination.backup_auto_$timestamp"
if (Test-Path $fichierDestination) {
    Copy-Item $fichierDestination $backupPath -Force
    Write-Host "  OK - Backup cree : show.html.twig.backup_auto_$timestamp" -ForegroundColor Green
} else {
    Write-Host "  INFO - Pas de fichier existant a sauvegarder" -ForegroundColor Yellow
}

# Etape 3 : Copier le fichier corrigé
Write-Host "[3/5] Copie du fichier corrige..." -ForegroundColor Yellow
Copy-Item $fichierDownload $fichierDestination -Force
Write-Host "  OK - Fichier copie !" -ForegroundColor Green

# Etape 4 : Vider le cache Symfony
Write-Host "[4/5] Vidage du cache Symfony..." -ForegroundColor Yellow
Set-Location $projectPath
Remove-Item -Recurse -Force "$projectPath\var\cache\*" -ErrorAction SilentlyContinue
Write-Host "  OK - Cache vide !" -ForegroundColor Green

# Etape 5 : Vérifier la taille du fichier
Write-Host "[5/5] Verification finale..." -ForegroundColor Yellow
$tailleFichier = (Get-Item $fichierDestination).Length
Write-Host "  Taille du fichier : $tailleFichier octets" -ForegroundColor Cyan
if ($tailleFichier -gt 30000) {
    Write-Host "  OK - Fichier de bonne taille !" -ForegroundColor Green
} else {
    Write-Host "  ATTENTION - Fichier plus petit que prevu" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "   CORRECTION TERMINEE AVEC SUCCES !" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "PROCHAINES ETAPES :" -ForegroundColor Yellow
Write-Host "1. Recharge la page dans ton navigateur (Ctrl+F5)" -ForegroundColor White
Write-Host "2. Verifie que les textes sont corrects" -ForegroundColor White
Write-Host ""
pause