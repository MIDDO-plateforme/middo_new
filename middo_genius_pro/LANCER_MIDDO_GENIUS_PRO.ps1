# MIDDO GENIUS PRO - LANCEUR AUTOMATIQUE
# Demarre tout en 1 clic

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - LANCEMENT AUTO   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ProjectPath = "C:\Users\MBANE LOKOTA\middo_new\middo_genius_pro"
$BackendPath = "$ProjectPath\backend"
$InterfacePath = "$ProjectPath\interface.html"

# Etape 1: Verification Python
Write-Host "[1/4] Verification Python..." -ForegroundColor Yellow
try {
    $pythonVersion = python --version 2>&1
    Write-Host "[OK] $pythonVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Python non trouve!" -ForegroundColor Red
    pause
    exit
}

# Etape 2: Verification environnement
Write-Host "[2/4] Verification environnement..." -ForegroundColor Yellow
if (Test-Path "$BackendPath\venv\Scripts\python.exe") {
    Write-Host "[OK] Environnement virtuel pret" -ForegroundColor Green
} else {
    Write-Host "[ERREUR] Environnement manquant!" -ForegroundColor Red
    pause
    exit
}

# Etape 3: Demarrage serveur
Write-Host "[3/4] Demarrage du serveur..." -ForegroundColor Yellow
Write-Host ""

Push-Location $BackendPath

$ServerJob = Start-Job -ScriptBlock {
    param($BackendPath)
    Set-Location $BackendPath
    & ".\venv\Scripts\Activate.ps1"
    python -m uvicorn app.main:app --host 0.0.0.0 --port 8000
} -ArgumentList $BackendPath

Write-Host "[OK] Serveur demarre (Job ID: $($ServerJob.Id))" -ForegroundColor Green

# Attente serveur pret
Write-Host "Attente du serveur..." -ForegroundColor Yellow
$attempt = 0
$ready = $false

while ($attempt -lt 60 -and -not $ready) {
    Start-Sleep -Seconds 1
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8000/health" -TimeoutSec 2 -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            $ready = $true
            Write-Host "[OK] Serveur operationnel!" -ForegroundColor Green
        }
    } catch {
        $attempt++
    }
}

if (-not $ready) {
    Write-Host "[ERREUR] Serveur ne repond pas" -ForegroundColor Red
    Stop-Job $ServerJob
    Remove-Job $ServerJob
    Pop-Location
    pause
    exit
}

Pop-Location

# Etape 4: Ouverture interface
Write-Host "[4/4] Ouverture interface..." -ForegroundColor Yellow

if (Test-Path $InterfacePath) {
    Start-Process $InterfacePath
    Write-Host "[OK] Interface ouverte!" -ForegroundColor Green
} else {
    Write-Host "[ATTENTION] Interface non trouvee" -ForegroundColor Yellow
    Start-Process "http://localhost:8000/docs"
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO OPERATIONNEL   " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Interface: Navigateur ouvert" -ForegroundColor Green
Write-Host "API: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Docs: http://localhost:8000/docs" -ForegroundColor Cyan
Write-Host ""
Write-Host "Pour arreter: Ferme cette fenetre" -ForegroundColor Yellow
Write-Host ""

# Garder ouvert
Write-Host "Serveur en cours d'execution..." -ForegroundColor Yellow
Write-Host "Appuie sur une touche pour arreter..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Arret
Write-Host "Arret du serveur..." -ForegroundColor Yellow
Stop-Job $ServerJob
Remove-Job $ServerJob
Write-Host "[OK] Serveur arrete" -ForegroundColor Green
Write-Host ""
