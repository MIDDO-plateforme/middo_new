cd "C:\Users\MBANE LOKOTA\middo_new"
$token = Read-Host "Entre ton token Render (rnd_...)"
Write-Host "SESSION 23 : REBUILD RENDER DIRECT" -ForegroundColor Yellow
$RENDER_API_KEY = $token
$SERVICE_NAME = "middo-app"
$TEST_URL = "https://middo-app.onrender.com/annuaire"
$HEADERS = @{"Authorization" = "Bearer $RENDER_API_KEY"; "Content-Type" = "application/json"}
Write-Host "Token: $($RENDER_API_KEY.Substring(0,10))..." -ForegroundColor Green
Write-Host "Recherche service..." -ForegroundColor Cyan
$response = Invoke-RestMethod -Uri "https://api.render.com/v1/services" -Method Get -Headers $HEADERS
$service = $response | Where-Object { $_.service.name -eq $SERVICE_NAME } | Select-Object -First 1
if (-not $service) { Write-Host "Service non trouvé!" -ForegroundColor Red; Read-Host; exit 1 }
$SERVICE_ID = $service.service.id
Write-Host "Service trouvé: $SERVICE_ID" -ForegroundColor Green
Write-Host "Lancement rebuild SANS CACHE..." -ForegroundColor Cyan
$body = @{clearCache = "clear"} | ConvertTo-Json
$deploy = Invoke-RestMethod -Uri "https://api.render.com/v1/services/$SERVICE_ID/deploys" -Method Post -Headers $HEADERS -Body $body
$DEPLOY_ID = $deploy.id
Write-Host "Rebuild lancé: $DEPLOY_ID" -ForegroundColor Green
Write-Host "Attente déploiement (7-10 min)..." -ForegroundColor Yellow
$MAX_ATTEMPTS = 60
$attempt = 0
$lastStatus = ""
while ($attempt -lt $MAX_ATTEMPTS) {
    try {
        $status = Invoke-RestMethod -Uri "https://api.render.com/v1/services/$SERVICE_ID/deploys/$DEPLOY_ID" -Method Get -Headers $HEADERS
        $currentStatus = $status.status
        if ($currentStatus -ne $lastStatus) {
            Write-Host "Statut: $currentStatus" -ForegroundColor Cyan
            $lastStatus = $currentStatus
        }
        if ($currentStatus -eq "live") {
            Write-Host ""
            Write-Host "DÉPLOIEMENT RÉUSSI!" -ForegroundColor Green
            Write-Host ""
            break
        }
        if ($currentStatus -eq "build_failed" -or $currentStatus -eq "deploy_failed") {
            Write-Host ""
            Write-Host "ÉCHEC DU DÉPLOIEMENT!" -ForegroundColor Red
            Write-Host ""
            Read-Host "Appuie sur Entrée"
            exit 1
        }
        Start-Sleep -Seconds 10
        $attempt++
    } catch {
        Write-Host "Erreur, réessai..." -ForegroundColor Yellow
        Start-Sleep -Seconds 10
        $attempt++
    }
}
Write-Host "Test /annuaire..." -ForegroundColor Cyan
Start-Sleep -Seconds 5
try {
    $response = Invoke-WebRequest -Uri $TEST_URL -Method Get -SkipHttpErrorCheck
    $statusCode = $response.StatusCode
    $content = $response.Content
    Write-Host "Code HTTP: $statusCode" -ForegroundColor White
    if ($statusCode -eq 200 -and $content -match "ANNUAIRE") {
        Write-Host ""
        Write-Host "═══════════════════════════════════════" -ForegroundColor Green
        Write-Host "  ANNUAIRE OK! 🎉" -ForegroundColor Green
        Write-Host "  MIDDO: 11/11 PAGES (100%)! 💪" -ForegroundColor Green
        Write-Host "═══════════════════════════════════════" -ForegroundColor Green
        Write-Host ""
    } elseif ($statusCode -eq 401) {
        Write-Host ""
        Write-Host "═══════════════════════════════════════" -ForegroundColor Red
        Write-Host "  TOUJOURS 401! 🚨" -ForegroundColor Red
        Write-Host "═══════════════════════════════════════" -ForegroundColor Red
        Write-Host ""
    } else {
        Write-Host "Code inattendu: $statusCode" -ForegroundColor Yellow
    }
} catch {
    Write-Host "Erreur test: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""
Read-Host "Appuie sur Entrée pour fermer"
