$file = "C:\Users\MBANE LOKOTA\middo_new\templates\project\show.html.twig"
Write-Host "Verification du fichier..." -ForegroundColor Yellow

if (Test-Path $file) {
    Write-Host "Fichier trouve!" -ForegroundColor Green
    
    $content = Get-Content $file -Raw -Encoding UTF8
    Write-Host "Taille: $($content.Length) caracteres" -ForegroundColor Cyan
    
    $content = $content.Replace('PropriÃƒÆ''Ã‚Â©taire', 'Proprietaire')
    $content = $content.Replace('AmÃƒÆ''Ã‚Â©liorer', 'Ameliorer')
    $content = $content.Replace('CrÃƒÆ''Ã‚Â©ÃƒÆ''Ã‚Â©', 'Cree')
    $content = $content.Replace('Retour ÃƒÆ''Ã‚Â ', 'Retour a')
    $content = $content.Replace('ÃƒÂ°Ã…Â¸Ã¢â‚¬â„¢Ã‚Â¬', '💬')
    $content = $content.Replace('ÃƒÂ°Ã…Â¸Ã¢â‚¬â„¢Ã‚Â¡', '💡')
    $content = $content.Replace('ÃƒÂ°Ã…Â¸Ã…Â½Ã‚Â¯', '🎯')
    $content = $content.Replace('ÃƒÂ°Ã…Â¸Ã‹Å"Ã…Â ', '😊')
    $content = $content.Replace('analyse©', 'analyse')
    $content = $content.Replace('analyseƒÂ©', 'analyse')
    $content = $content.Replace('complÃƒÂ¨te', 'complete')
    $content = $content.Replace('CatÃ©gorie', 'Categorie')
    
    Set-Content $file $content -Encoding UTF8
    Write-Host "CORRIGE!" -ForegroundColor Green
    
    Remove-Item "var\cache" -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "Cache supprime - Rechargez Ctrl+Shift+R" -ForegroundColor Cyan
} else {
    Write-Host "FICHIER INTROUVABLE!" -ForegroundColor Red
}
