# MIDDO GENIUS PRO - Installation Automatique
# Version 1.1 - CORRIGEE

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - INSTALLATION   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ProjectRoot = "C:\Users\MBANE LOKOTA\middo_new"
$GeniusProPath = "$ProjectRoot\middo_genius_pro"

Write-Host "[INFO] Verification des prerequis..." -ForegroundColor Yellow
Write-Host ""

# Verification Python
$pythonInstalled = $false
try {
    $pythonVersion = python --version 2>&1
    if ($pythonVersion -match "Python") {
        Write-Host "[OK] Python installe: $pythonVersion" -ForegroundColor Green
        $pythonInstalled = $true
    }
} catch {
    Write-Host "[ERREUR] Python non installe!" -ForegroundColor Red
    Write-Host "Install avec: winget install Python.Python.3.11" -ForegroundColor Yellow
    pause
    exit
}

Write-Host ""
Write-Host "[INFO] Creation de la structure..." -ForegroundColor Yellow
Write-Host ""

# Creation des dossiers principaux
$folders = @(
    "$GeniusProPath",
    "$GeniusProPath\backend",
    "$GeniusProPath\backend\app",
    "$GeniusProPath\backend\app\api",
    "$GeniusProPath\backend\app\services",
    "$GeniusProPath\symfony-bundle",
    "$GeniusProPath\docker",
    "$GeniusProPath\scripts",
    "$GeniusProPath\docs"
)

foreach ($folder in $folders) {
    if (-not (Test-Path $folder)) {
        New-Item -ItemType Directory -Force -Path $folder | Out-Null
        Write-Host "[OK] Cree: $folder" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[INFO] Creation des fichiers..." -ForegroundColor Yellow
Write-Host ""

# Fichier requirements.txt
$reqFile = "$GeniusProPath\backend\requirements.txt"
$requirements = "fastapi==0.104.1`nuvicorn[standard]==0.24.0`nopenai==1.3.5`npython-dotenv==1.0.0`npydantic==2.5.0"
Set-Content -Path $reqFile -Value $requirements
Write-Host "[OK] Cree: requirements.txt" -ForegroundColor Green

# Fichier .env.example
$envFile = "$GeniusProPath\backend\.env.example"
$envContent = "OPENAI_API_KEY=your_key_here`nDATABASE_URL=postgresql://localhost:5432/middo`nSECRET_KEY=change_me"
Set-Content -Path $envFile -Value $envContent
Write-Host "[OK] Cree: .env.example" -ForegroundColor Green

# Fichier README.md
$readmeFile = "$GeniusProPath\README.md"
$readmeContent = "# MIDDO GENIUS PRO`n`nAgent IA Multi-Expertise`n`n## Installation`n`nLance: .\install-middo-genius-pro.ps1"
Set-Content -Path $readmeFile -Value $readmeContent
Write-Host "[OK] Cree: README.md" -ForegroundColor Green

Write-Host ""
Write-Host "[INFO] Creation environnement Python..." -ForegroundColor Yellow
Write-Host ""

Push-Location "$GeniusProPath\backend"

try {
    python -m venv venv
    Write-Host "[OK] Environnement virtuel cree" -ForegroundColor Green
    
    $activateScript = ".\venv\Scripts\Activate.ps1"
    if (Test-Path $activateScript) {
        & $activateScript
        Write-Host "[OK] Environnement active" -ForegroundColor Green
        
        Write-Host "[INFO] Installation des dependances..." -ForegroundColor Yellow
        pip install --quiet --upgrade pip
        pip install --quiet -r requirements.txt
        Write-Host "[OK] Dependances installees" -ForegroundColor Green
    }
} catch {
    Write-Host "[ERREUR] Probleme avec Python" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Pop-Location

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   INSTALLATION TERMINEE   " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Structure creee dans:" -ForegroundColor Yellow
Write-Host "$GeniusProPath" -ForegroundColor Cyan
Write-Host ""

pause
