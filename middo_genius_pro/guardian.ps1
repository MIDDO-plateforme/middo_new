# ============================================
# MIDDO GENIUS PRO - GUARDIAN SYSTEM
# Surveillance et Auto-Reparation
# Version 1.0
# ============================================

param(
    [int]$CheckInterval = 30,  # Verification toutes les 30 secondes
    [int]$MaxRetries = 3,      # Nombre de tentatives avant alerte
    [switch]$AutoRestart = $true
)

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   MIDDO GENIUS PRO - GUARDIAN   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ProjectPath = "C:\Users\MBANE LOKOTA\middo_new\middo_genius_pro"
$BackendPath = "$ProjectPath\backend"
$LogFile = "$ProjectPath\guardian.log"
$AlertFile = "$ProjectPath\ALERTES.txt"
$HealthUrl = "http://localhost:8000/health"

# Fonction de log
function Write-GuardianLog {
    param($Message, $Level = "INFO", $Color = "White")
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogMessage = "[$Timestamp] [$Level] $Message"
    Write-Host $LogMessage -ForegroundColor $Color
    Add-Content -Path $LogFile -Value $LogMessage
}

# Fonction d'alerte
function Send-Alert {
    param($Message)
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $Alert = "[$Timestamp] ALERTE: $Message"
    Add-Content -Path $AlertFile -Value $Alert
    Write-Host $Alert -ForegroundColor Red -BackgroundColor Yellow
}

# Fonction de verification de sante
function Test-ServerHealth {
    try {
        $response = Invoke-WebRequest -Uri $HealthUrl -TimeoutSec 5 -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            $health = $response.Content | ConvertFrom-Json
            if ($health.status -eq "healthy" -and $health.openai_configured -eq $true) {
                return $true
            }
        }
        return $false
    } catch {
        return $false
    }
}

# Fonction de redemarrage
function Restart-Server {
    Write-GuardianLog "Tentative de redemarrage du serveur..." "WARN" "Yellow"
    
    # Arret des processus Python existants
    Get-Process python -ErrorAction SilentlyContinue | Stop-Process -Force
    Start-Sleep -Seconds 3
    
    # Redemarrage
    Push-Location $BackendPath
    
    $ServerJob = Start-Job -ScriptBlock {
        param($BackendPath)
        Set-Location $BackendPath
        & ".\venv\Scripts\Activate.ps1"
        python -m uvicorn app.main:app --host 0.0.0.0 --port 8000
    } -ArgumentList $BackendPath
    
    Pop-Location
    
    # Attente demarrage
    $attempt = 0
    $maxWait = 30
    while ($attempt -lt $maxWait) {
        Start-Sleep -Seconds 2
        if (Test-ServerHealth) {
            Write-GuardianLog "Serveur redemarre avec succes (Job ID: $($ServerJob.Id))" "SUCCESS" "Green"
            return $ServerJob.Id
        }
        $attempt++
    }
    
    Write-GuardianLog "Echec du redemarrage apres $maxWait tentatives" "ERROR" "Red"
    Send-Alert "Impossible de redemarrer le serveur apres $maxWait tentatives"
    return $null
}

# Fonction de detection d'anomalies
function Test-Anomalies {
    $anomalies = @()
    
    # Verification processus Python
    $pythonProcesses = Get-Process python -ErrorAction SilentlyContinue
    if ($pythonProcesses.Count -gt 5) {
        $anomalies += "Trop de processus Python detectes: $($pythonProcesses.Count)"
    }
    
    # Verification utilisation memoire
    if ($pythonProcesses) {
        $totalMemory = ($pythonProcesses | Measure-Object WorkingSet64 -Sum).Sum / 1MB
        if ($totalMemory -gt 1000) {
            $anomalies += "Utilisation memoire elevee: $([math]::Round($totalMemory, 2)) MB"
        }
    }
    
    # Verification taille des logs
    if (Test-Path $LogFile) {
        $logSize = (Get-Item $LogFile).Length / 1MB
        if ($logSize -gt 50) {
            $anomalies += "Fichier log tres volumineux: $([math]::Round($logSize, 2)) MB"
        }
    }
    
    return $anomalies
}

# Initialisation
Write-GuardianLog "Demarrage du Guardian System" "INFO" "Cyan"
Write-GuardianLog "Intervalle de verification: $CheckInterval secondes" "INFO" "Cyan"
Write-GuardianLog "Auto-restart: $AutoRestart" "INFO" "Cyan"
Write-Host ""

$consecutiveFailures = 0
$totalChecks = 0
$totalFailures = 0
$startTime = Get-Date

# Boucle principale de surveillance
while ($true) {
    $totalChecks++
    
    Write-Host "[$(Get-Date -Format 'HH:mm:ss')] " -NoNewline -ForegroundColor Gray
    Write-Host "Check #$totalChecks - " -NoNewline
    
    # Verification de sante
    if (Test-ServerHealth) {
        Write-Host "OK" -ForegroundColor Green
        $consecutiveFailures = 0
        
        # Detection d'anomalies
        $anomalies = Test-Anomalies
        if ($anomalies.Count -gt 0) {
            foreach ($anomaly in $anomalies) {
                Write-GuardianLog $anomaly "WARN" "Yellow"
            }
        }
        
    } else {
        Write-Host "ECHEC" -ForegroundColor Red
        $consecutiveFailures++
        $totalFailures++
        
        Write-GuardianLog "Serveur ne repond pas (echec consecutif: $consecutiveFailures)" "ERROR" "Red"
        
        # Auto-restart si active et seuil atteint
        if ($AutoRestart -and $consecutiveFailures -ge $MaxRetries) {
            Write-GuardianLog "Seuil d'echec atteint ($MaxRetries), declenchement auto-restart" "WARN" "Yellow"
            Send-Alert "Serveur down depuis $consecutiveFailures checks, redemarrage auto en cours"
            
            $jobId = Restart-Server
            if ($jobId) {
                $consecutiveFailures = 0
            } else {
                Send-Alert "CRITIQUE: Impossible de redemarrer le serveur automatiquement!"
            }
        }
    }
    
    # Statistiques toutes les 20 checks
    if ($totalChecks % 20 -eq 0) {
        $uptime = (Get-Date) - $startTime
        $successRate = [math]::Round((($totalChecks - $totalFailures) / $totalChecks) * 100, 2)
        Write-Host ""
        Write-Host "--- STATISTIQUES ---" -ForegroundColor Cyan
        Write-Host "Uptime Guardian: $($uptime.Hours)h $($uptime.Minutes)m" -ForegroundColor Cyan
        Write-Host "Checks totaux: $totalChecks" -ForegroundColor Cyan
        Write-Host "Taux de succes: $successRate%" -ForegroundColor Cyan
        Write-Host "--------------------" -ForegroundColor Cyan
        Write-Host ""
    }
    
    Start-Sleep -Seconds $CheckInterval
}
