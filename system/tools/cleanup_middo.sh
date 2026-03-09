#!/bin/bash

echo "=== MIDDO CLEANUP SCRIPT ==="

# 1) Create structure
echo "Creating folders..."
mkdir -p dev_scripts archives private private/sql private/scripts private/keys dev_scripts/public_legacy

# 2) Move test PHP scripts
echo "Moving test scripts to dev_scripts/..."
mv test_*.php dev_scripts/ 2>/dev/null || true
mv test_api_*.php dev_scripts/ 2>/dev/null || true
mv test_openai*.php dev_scripts/ 2>/dev/null || true
mv test_service.php dev_scripts/ 2>/dev/null || true
mv test_simple.php dev_scripts/ 2>/dev/null || true
mv test_notifications.php dev_scripts/ 2>/dev/null || true
mv test_controllers.php dev_scripts/ 2>/dev/null || true
mv test_fastapi.php dev_scripts/ 2>/dev/null || true

# 3) Move backups, logs, memos, temp files
echo "Moving backups and logs to archives/..."
mv backup_* archives/ 2>/dev/null || true
mv MEMO_SESSION_* archives/ 2>/dev/null || true
mv RAPPORT_SESSION_* archives/ 2>/dev/null || true
mv audit_*.log archives/ 2>/dev/null || true
mv server_debug.log archives/ 2>/dev/null || true
mv temp_* archives/ 2>/dev/null || true
mv Syntaxe archives/ 2>/dev/null || true
mv Test archives/ 2>/dev/null || true
mv list.txt archives/ 2>/dev/null || true

# 4) Move SQL files and sensitive config
echo "Moving SQL and sensitive files to private/..."
mv *.sql private/sql/ 2>/dev/null || true
mv *.ps1 private/scripts/ 2>/dev/null || true
mv config/"Différents Clè.md" private/keys/ 2>/dev/null || true
mv config/accessKeys.csv private/keys/ 2>/dev/null || true

# 5) Clean public/ (keep only index.php)
echo "Cleaning public/ folder..."
find public -maxdepth 1 -type f ! -name 'index.php' -exec mv {} dev_scripts/public_legacy/ \;

# 6) Final report
echo "=== CLEANUP COMPLETE ==="
echo "Content of dev_scripts/:"
ls -R dev_scripts || true

echo "Content of archives/:"
ls -R archives || true

echo "Content of private/:"
ls -R private || true

echo "=== END ==="
