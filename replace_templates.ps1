# ================================================================
# SESSION 25 - TEMPLATE REPLACEMENT SCRIPT
# ================================================================

Clear-Host

Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " SESSION 25 - TEMPLATE REPLACEMENT SCRIPT" -ForegroundColor Magenta
Write-Host " Replace existing templates with premium versions" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray

# Navigate to project directory
Write-Host " Navigating to project directory..." -ForegroundColor Cyan
cd "C:\Users\MBANE LOKOTA\middo_new"

if (-not (Test-Path "composer.json")) {
    Write-Host " ERROR: composer.json not found!" -ForegroundColor Red
    exit 1
}

Write-Host " Project directory OK" -ForegroundColor Green

# Backup existing files
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " CREATING BACKUP" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "backups/SESSION_25_REPLACE_$timestamp"

if (-not (Test-Path "backups")) {
    New-Item -ItemType Directory -Path "backups" | Out-Null
}
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

Copy-Item templates/project/index.html.twig "$backupDir/index.html.twig.old"
Copy-Item templates/project/show.html.twig "$backupDir/show.html.twig.old"
Copy-Item templates/project/new.html.twig "$backupDir/new.html.twig.old"
Copy-Item templates/project/edit.html.twig "$backupDir/edit.html.twig.old"

Write-Host " Backup created: $backupDir" -ForegroundColor Green

# STEP 1/4: Edit index.html.twig
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " STEP 1/4: EDITING index.html.twig (LIST PAGE)" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " 1. Notepad will open with the current file" -ForegroundColor Yellow
Write-Host " 2. DELETE ALL existing content (Ctrl+A, Delete)" -ForegroundColor Yellow
Write-Host " 3. Go to HTML document (Option 1 - Liste des projets)" -ForegroundColor Yellow
Write-Host " 4. Copy code from grey <div class='code-block'>" -ForegroundColor Yellow
Write-Host " 5. Paste in Notepad (Ctrl+V)" -ForegroundColor Yellow
Write-Host " 6. Save (Ctrl+S) and CLOSE Notepad" -ForegroundColor Yellow
Write-Host ""
Write-Host " Press ENTER to open index.html.twig in Notepad..."
$null = Read-Host

notepad templates/project/index.html.twig

Write-Host " index.html.twig updated!" -ForegroundColor Green
Write-Host ""

# STEP 2/4: Edit show.html.twig
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " STEP 2/4: EDITING show.html.twig (DETAILS PAGE)" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " Same process for show.html.twig" -ForegroundColor Yellow
Write-Host " Use HTML document (Option 3 - Page Details du Projet)" -ForegroundColor Yellow
Write-Host " Press ENTER to open show.html.twig in Notepad..."
$null = Read-Host

notepad templates/project/show.html.twig

Write-Host " show.html.twig updated!" -ForegroundColor Green
Write-Host ""

# STEP 3/4: Edit new.html.twig
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " STEP 3/4: EDITING new.html.twig (CREATION FORM)" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " Same process for new.html.twig" -ForegroundColor Yellow
Write-Host " Use HTML document (Option 2 - Formulaire de Creation)" -ForegroundColor Yellow
Write-Host " Press ENTER to open new.html.twig in Notepad..."
$null = Read-Host

notepad templates/project/new.html.twig

Write-Host " new.html.twig updated!" -ForegroundColor Green
Write-Host ""

# STEP 4/4: Edit edit.html.twig
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " STEP 4/4: EDITING edit.html.twig (EDIT FORM)" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " Same process for edit.html.twig" -ForegroundColor Yellow
Write-Host " Use HTML document (Option 4 - Formulaire d'Edition)" -ForegroundColor Yellow
Write-Host " Press ENTER to open edit.html.twig in Notepad..."
$null = Read-Host

notepad templates/project/edit.html.twig

Write-Host " edit.html.twig updated!" -ForegroundColor Green
Write-Host ""

# Git operations
Write-Host "============================================================" -ForegroundColor DarkGray
Write-Host " GIT OPERATIONS" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor DarkGray

Write-Host " Adding files to git..." -ForegroundColor Cyan
git add templates/project/index.html.twig
git add templates/project/show.html.twig
git add templates/project/new.html.twig
git add templates/project/edit.html.twig

Write-Host " Committing..." -ForegroundColor Cyan
git commit -m "SESSION 25: Replace Project Templates with Premium Versions

Updated 4 Templates Twig with premium design:
- index.html.twig (~15 KB): liste responsive + recherche + filtres
- show.html.twig (~18 KB): details + timeline + stats + permissions
- new.html.twig (~20 KB): creation avec labels flottants + validation
- edit.html.twig (~22 KB): edition + metadata + modal suppression

Design MIDDO complet (#f4a261)
Responsive mobile/tablet/desktop
Animations smooth (fadeIn, slideUp, hover)
Permissions Voter + CSRF tokens
Accessibilite (ARIA labels, keyboard nav)

Backend deja deploye (commit 4981cc4)
URL: https://middo-app.onrender.com/projets"

if ($LASTEXITCODE -eq 0) {
    Write-Host " Commit successful!" -ForegroundColor Green
    $commitHash = git rev-parse HEAD
    $shortHash = $commitHash.Substring(0, 7)
    Write-Host " Commit hash: $shortHash" -ForegroundColor Cyan
} else {
    Write-Host " Commit failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host " Press ENTER to push to GitHub and deploy to Render..."
$null = Read-Host

Write-Host " Pushing to origin/main..." -ForegroundColor Cyan
git push origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host " Push successful!" -ForegroundColor Green
    Write-Host ""
    Write-Host "============================================================" -ForegroundColor DarkGray
    Write-Host " DEPLOYMENT COMPLETE" -ForegroundColor Green
    Write-Host "============================================================" -ForegroundColor DarkGray
    Write-Host " Templates updated: 4/4" -ForegroundColor Green
    Write-Host " Render will deploy in ~3-5 minutes" -ForegroundColor Yellow
    Write-Host " Test URL: https://middo-app.onrender.com/projets" -ForegroundColor Cyan
    Write-Host "============================================================" -ForegroundColor DarkGray
} else {
    Write-Host " Push failed!" -ForegroundColor Red
    exit 1
}

Write-Host " Press any key to close..."
$null = [Console]::ReadKey()