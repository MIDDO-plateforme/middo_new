# MIDDO GENIUS PRO - Script de Demarrage
# Version 1.0

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - DEMARRAGE   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$BackendPath = "C:\Users\MBANE LOKOTA\middo_new\middo_genius_pro\backend"

Write-Host "[INFO] Demarrage du serveur IA..." -ForegroundColor Yellow
Write-Host ""

Push-Location $BackendPath

# Activation environnement virtuel
& ".\venv\Scripts\Activate.ps1"
Write-Host "[OK] Environnement Python active" -ForegroundColor Green

# Verification .env
if (-not (Test-Path ".env")) {
    Write-Host "[ATTENTION] Fichier .env manquant!" -ForegroundColor Yellow
    Write-Host "Copie .env.example vers .env et configure tes cles API" -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "[OK] Fichier .env cree depuis .env.example" -ForegroundColor Green
}

Write-Host ""
Write-Host "[INFO] Lancement du serveur FastAPI..." -ForegroundColor Yellow
Write-Host "URL: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Documentation API: http://localhost:8000/docs" -ForegroundColor Cyan
Write-Host ""
Write-Host "Appuie sur Ctrl+C pour arreter le serveur" -ForegroundColor Yellow
Write-Host ""

# Demarrage du serveur
python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8000

Pop-Location
