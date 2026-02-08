# MIDDO GENIUS PRO - Demarrage avec Guardian
# Version 1.0

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO + GUARDIAN   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ProjectPath = "C:\Users\MBANE LOKOTA\middo_new\middo_genius_pro"
$BackendPath = "$ProjectPath\backend"
$InterfacePath = "$ProjectPath\interface.html"

# Etape 1: Demarrage du serveur
Write-Host "[1/3] Demarrage du serveur..." -ForegroundColor Yellow

Push-Location $BackendPath

$ServerJob = Start-Job -ScriptBlock {
    param($BackendPath)
    Set-Location $BackendPath
    & ".\venv\Scripts\Activate.ps1"
    python -m uvicorn app.main:app --host 0.0.0.0 --port 8000
} -ArgumentList $BackendPath

Pop-Location

# Attente serveur pret
Write-Host "Attente du serveur..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

try {
    $health = Invoke-WebRequest -Uri "http://localhost:8000/health" -TimeoutSec 5
    Write-Host "[OK] Serveur operationnel!" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Serveur ne repond pas" -ForegroundColor Red
    Stop-Job $ServerJob
    Remove-Job $ServerJob
    pause
    exit
}

# Etape 2: Ouverture interface
Write-Host "[2/3] Ouverture interface..." -ForegroundColor Yellow

if (Test-Path $InterfacePath) {
    Start-Process $InterfacePath
    Write-Host "[OK] Interface ouverte!" -ForegroundColor Green
} else {
    Write-Host "[ATTENTION] Interface non trouvee" -ForegroundColor Yellow
}

# Etape 3: Demarrage Guardian en arriere-plan
Write-Host "[3/3] Demarrage du Guardian..." -ForegroundColor Yellow

$GuardianJob = Start-Job -ScriptBlock {
    param($ProjectPath)
    Set-Location $ProjectPath
    & ".\guardian.ps1" -CheckInterval 30 -MaxRetries 3 -AutoRestart
} -ArgumentList $ProjectPath

Write-Host "[OK] Guardian actif (Job ID: $($GuardianJob.Id))" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   TOUT EST OPERATIONNEL   " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Serveur API: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Guardian: Surveillance active" -ForegroundColor Green
Write-Host ""
Write-Host "Pour arreter: Ferme cette fenetre" -ForegroundColor Yellow
Write-Host ""

# Garder ouvert
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Arret propre
Write-Host "Arret en cours..." -ForegroundColor Yellow
Stop-Job $ServerJob, $GuardianJob
Remove-Job $ServerJob, $GuardianJob
Write-Host "[OK] Tout est arrete" -ForegroundColor Green
