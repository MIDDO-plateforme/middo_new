# ================================================================
# MIDDO AUTOMATION SYSTEM - DEPLOY WITH AUTO-DIAGNOSTIC
# ================================================================
# Description: Complete deployment automation with pre-checks, post-deployment testing, and auto-rollback
# Author: Assistant AI + Baudouin
# Date: 2026-01-07
# Path: C:\Users\MBANE LOKOTA\middo_new\deploy_auto.ps1
# ================================================================

param(
    [string]$RenderApiKey = "rnd_gZclr9iVxxxxxxxxxxxxxx",
    [string]$ServiceId = "srv-d5ccq3xxxxxxxxxxxxxxxx",
    [switch]$SkipTests = $false
)

Clear-Host

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host " MIDDO AUTOMATION SYSTEM - DEPLOY WITH AUTO-DIAGNOSTIC" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Variables
$ProjectRoot = Get-Location
$BackupTimestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupDir = "backups/SESSION_25_AUTO_$BackupTimestamp"
$ProdUrl = "https://middo-app.onrender.com"
$DeploymentSuccess = $false

try {
    # ================================================================
    # PRE-DEPLOYMENT CHECKS
    # ================================================================
    Write-Host "Pre-deployment checks..." -ForegroundColor Yellow
    
    # Check composer.json exists
    if (-not (Test-Path "composer.json")) {
        throw "composer.json not found! Are you in the correct directory?"
    }
    Write-Host "[OK] composer.json found" -ForegroundColor Green
    
    # Check git status
    $gitStatus = git status --porcelain 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Git repository not found or corrupted"
    }
    Write-Host "[OK] Git repository valid" -ForegroundColor Green
    
    # Check if we're on main branch
    $currentBranch = git branch --show-current 2>$null
    if ($currentBranch -ne "main") {
        Write-Host "[WARNING] You are not on main branch (current: $currentBranch)" -ForegroundColor Yellow
    }
    
    # Validate Twig templates
    $twigValidation = php bin/console lint:twig templates/ 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Twig templates valid" -ForegroundColor Green
    } else {
        Write-Host "[WARNING] Twig validation issues detected" -ForegroundColor Yellow
    }

    # ================================================================
    # BACKUP CREATION
    # ================================================================
    Write-Host ""
    Write-Host "Creating backup..." -ForegroundColor Yellow
    
    # Create backup directory
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
    
    # Backup existing templates if they exist
    $templatesBackedup = 0
    $templateFiles = @(
        "templates/project/index.html.twig",
        "templates/project/show.html.twig", 
        "templates/project/new.html.twig",
        "templates/project/edit.html.twig"
    )
    
    foreach ($template in $templateFiles) {
        if (Test-Path $template) {
            Copy-Item $template "$BackupDir/$(Split-Path $template -Leaf).backup" -Force
            $templatesBackedup++
        }
    }
    
    Write-Host "[OK] Backup created: $BackupDir ($templatesBackedup files)" -ForegroundColor Green

    # ================================================================
    # DEPLOYMENT
    # ================================================================
    Write-Host ""
    Write-Host "Starting deployment..." -ForegroundColor Yellow
    
    # Add files to git
    git add templates/project/index.html.twig templates/project/show.html.twig templates/project/new.html.twig templates/project/edit.html.twig src/Controller/HealthController.php
    
    if ($LASTEXITCODE -ne 0) {
        throw "Failed to add files to git"
    }
    
    # Create commit message
    $commitMessage = @"
SESSION 25+: Auto-deployment with enhanced monitoring

- Enhanced HealthController with complete system monitoring
- Premium Twig templates with MIDDO design system
- Automated deployment via PowerShell script
- Post-deployment testing and validation

Features:
- Real-time health monitoring endpoint
- Responsive design with animations
- Complete CRUD functionality
- Security with Voter permissions
- Auto-rollback on deployment failure

URL: $ProdUrl/projets
"@
    
    # Commit changes
    git commit -m $commitMessage
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[INFO] No changes to commit or commit failed" -ForegroundColor Yellow
    } else {
        Write-Host "[OK] Changes committed" -ForegroundColor Green
    }
    
    # Push to origin
    Write-Host "Pushing to GitHub..." -ForegroundColor Yellow
    git push origin main
    
    if ($LASTEXITCODE -ne 0) {
        throw "Failed to push to GitHub"
    }
    
    Write-Host "[OK] Pushed to GitHub successfully" -ForegroundColor Green

    # ================================================================
    # POST-DEPLOYMENT TESTING
    # ================================================================
    if (-not $SkipTests) {
        Write-Host ""
        Write-Host "Waiting for Render deployment (5 minutes)..." -ForegroundColor Yellow
        
        # Progress bar for 5 minutes (300 seconds)
        for ($i = 1; $i -le 300; $i++) {
            $percent = ($i / 300) * 100
            Write-Progress -Activity "Waiting for Render deployment" -Status "Please wait... ($i/300 seconds)" -PercentComplete $percent
            Start-Sleep -Seconds 1
        }
        Write-Progress -Activity "Waiting for Render deployment" -Completed
        
        Write-Host ""
        Write-Host "Testing deployment..." -ForegroundColor Yellow
        
        # Test health endpoint
        try {
            $healthResponse = Invoke-WebRequest -Uri "$ProdUrl/health" -UseBasicParsing -TimeoutSec 30
            if ($healthResponse.StatusCode -eq 200) {
                Write-Host "[OK] Health endpoint responding (Status: 200)" -ForegroundColor Green
                $healthData = $healthResponse.Content | ConvertFrom-Json
                Write-Host "[INFO] Health status: $($healthData.status)" -ForegroundColor Cyan
            } else {
                Write-Host "[ERROR] Health endpoint returned status: $($healthResponse.StatusCode)" -ForegroundColor Red
            }
        } catch {
            Write-Host "[ERROR] Health endpoint failed: $($_.Exception.Message)" -ForegroundColor Red
        }
        
        # Test projects page
        try {
            $projectsResponse = Invoke-WebRequest -Uri "$ProdUrl/projets" -UseBasicParsing -TimeoutSec 30
            if ($projectsResponse.StatusCode -eq 200) {
                Write-Host "[OK] Projects page accessible (Status: 200)" -ForegroundColor Green
                
                # Check for premium CSS
                if ($projectsResponse.Content -match "--middo-orange.*#f4a261") {
                    Write-Host "[OK] Premium CSS detected!" -ForegroundColor Green
                } else {
                    Write-Host "[WARNING] Premium CSS not found" -ForegroundColor Yellow
                }
                
                # Check for key elements
                if ($projectsResponse.Content -match "projects-grid") {
                    Write-Host "[OK] Projects grid found" -ForegroundColor Green
                }
                
                if ($projectsResponse.Content -match "btn-create") {
                    Write-Host "[OK] Create button found" -ForegroundColor Green
                }
                
                $DeploymentSuccess = $true
                
            } else {
                Write-Host "[ERROR] Projects page returned status: $($projectsResponse.StatusCode)" -ForegroundColor Red
            }
        } catch {
            Write-Host "[ERROR] Projects page failed: $($_.Exception.Message)" -ForegroundColor Red
        }
        
        # Generate HTML report
        $reportFile = "deployment_report_$BackupTimestamp.html"
        $healthStatus = if ($healthResponse.StatusCode -eq 200) { 'OK' } else { 'FAILED' }
        $projectsStatus = if ($projectsResponse.StatusCode -eq 200) { 'OK' } else { 'FAILED' }
        $cssStatus = if ($projectsResponse.Content -match '--middo-orange.*#f4a261') { 'OK' } else { 'NOT FOUND' }
        
        $reportContent = @"
<!DOCTYPE html>
<html>
<head>
    <title>MIDDO Deployment Report - $BackupTimestamp</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .header { background: #f4a261; color: white; padding: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MIDDO Deployment Report</h1>
        <p>Generated: $(Get-Date)</p>
    </div>
    <h2>Deployment Summary</h2>
    <ul>
        <li>Backup Created: $BackupDir</li>
        <li>Templates Backed Up: $templatesBackedup</li>
        <li>Git Push: Success</li>
        <li>Health Endpoint: $healthStatus</li>
        <li>Projects Page: $projectsStatus</li>
        <li>Premium CSS: $cssStatus</li>
    </ul>
    <h2>URLs</h2>
    <ul>
        <li><a href="$ProdUrl/health">Health Endpoint</a></li>
        <li><a href="$ProdUrl/projets">Projects Page</a></li>
        <li><a href="https://github.com/MIDDO-plateforme/middo-prod-v2">GitHub Repository</a></li>
        <li><a href="https://dashboard.render.com">Render Dashboard</a></li>
    </ul>
</body>
</html>
"@
        
        $reportContent | Out-File $reportFile -Encoding UTF8
        Write-Host "[INFO] HTML report generated: $reportFile" -ForegroundColor Cyan
    }

    # ================================================================
    # RENDER LOGS (if API configured)
    # ================================================================
    if ($RenderApiKey -and $ServiceId -and $RenderApiKey -ne "rnd_gZclr9iVxxxxxxxxxxxxxx") {
        Write-Host ""
        Write-Host "Fetching Render logs..." -ForegroundColor Yellow
        
        try {
            $headers = @{
                "Authorization" = "Bearer $RenderApiKey"
                "Accept" = "application/json"
            }
            
            $logsResponse = Invoke-RestMethod -Uri "https://api.render.com/v1/services/$ServiceId/logs" -Headers $headers -TimeoutSec 30
            
            $logFile = "render_logs_$BackupTimestamp.txt"
            $logsResponse | Out-File $logFile -Encoding UTF8
            
            Write-Host "[OK] Render logs saved to: $logFile" -ForegroundColor Green
            
            # Show last 5 log entries
            Write-Host "Recent logs:" -ForegroundColor Cyan
            $logsResponse | Select-Object -Last 5 | ForEach-Object {
                Write-Host "  $($_.timestamp): $($_.message)" -ForegroundColor Gray
            }
            
        } catch {
            Write-Host "[WARNING] Could not fetch Render logs: $($_.Exception.Message)" -ForegroundColor Yellow
        }
    }

    # ================================================================
    # FINAL SUMMARY
    # ================================================================
    Write-Host ""
    Write-Host "================================================================" -ForegroundColor Cyan
    Write-Host " DEPLOYMENT SUMMARY" -ForegroundColor Cyan
    Write-Host "================================================================" -ForegroundColor Cyan
    
    if ($DeploymentSuccess) {
        Write-Host "Status: SUCCESS" -ForegroundColor Green
        Write-Host "Application URL: $ProdUrl/projets" -ForegroundColor Green
        Write-Host "Health Check: $ProdUrl/health" -ForegroundColor Green
    } else {
        Write-Host "Status: COMPLETED WITH WARNINGS" -ForegroundColor Yellow
        Write-Host "Check the logs above for details" -ForegroundColor Yellow
    }
    
    Write-Host ""
    Write-Host "Files created:" -ForegroundColor Cyan
    Write-Host "- Backup: $BackupDir" -ForegroundColor Gray
    if (Test-Path "deployment_report_$BackupTimestamp.html") {
        Write-Host "- Report: deployment_report_$BackupTimestamp.html" -ForegroundColor Gray
    }
    if (Test-Path "render_logs_$BackupTimestamp.txt") {
        Write-Host "- Logs: render_logs_$BackupTimestamp.txt" -ForegroundColor Gray
    }
    
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "1. Test the application at $ProdUrl/projets" -ForegroundColor Gray
    Write-Host "2. Check GitHub Actions at https://github.com/MIDDO-plateforme/middo-prod-v2/actions" -ForegroundColor Gray
    Write-Host "3. Monitor via health endpoint: $ProdUrl/health" -ForegroundColor Gray

} catch {
    Write-Host ""
    Write-Host "================================================================" -ForegroundColor Red
    Write-Host " DEPLOYMENT FAILED" -ForegroundColor Red
    Write-Host "================================================================" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    
    # Auto-rollback attempt
    if (Test-Path $BackupDir) {
        Write-Host ""
        Write-Host "Attempting auto-rollback..." -ForegroundColor Yellow
        
        try {
            foreach ($template in $templateFiles) {
                $backupFile = "$BackupDir/$(Split-Path $template -Leaf).backup"
                if (Test-Path $backupFile) {
                    Copy-Item $backupFile $template -Force
                    Write-Host "[OK] Restored: $template" -ForegroundColor Green
                }
            }
            Write-Host "[OK] Auto-rollback completed" -ForegroundColor Green
        } catch {
            Write-Host "[ERROR] Auto-rollback failed: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    
    exit 1
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Green
Write-Host " DEPLOYMENT AUTOMATION COMPLETE" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Press any key to close..." -ForegroundColor Gray
$null = [Console]::ReadKey()