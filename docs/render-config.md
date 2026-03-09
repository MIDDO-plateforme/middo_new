# 🛡️ Configuration Render pour MIDDO

## 📋 Paramètres recommandés

### ⚙️ Settings actuels
- **Auto-Deploy**: OFF (désactivé)  
- **Branch source**: main  
- **Deploy method**: Manuel uniquement

---

## 🚀 Workflow de déploiement

### 1️⃣ Développement
\\\ash
# Sur la branche develop
git checkout develop

# Faire les modifications
# ...

# Commit
git add .
git commit -m "feat: nouvelle fonctionnalité"
\\\

### 2️⃣ Tests locaux
\\\ash
# Tester l'application
php bin/console cache:clear
php -S localhost:8000 -t public/
\\\

### 3️⃣ Merge vers main
\\\ash
# Basculer sur main
git checkout main

# Merger develop
git merge develop

# Push
git push origin main
\\\

### 4️⃣ Déploiement manuel
1. Aller sur https://dashboard.render.com/
2. Sélectionner **middo-app**
3. Cliquer sur **Manual Deploy**
4. Sélectionner la branche **main**
5. Confirmer le déploiement

---

## ✅ Avantages de cette configuration

- 🔒 **Sécurité**: Pas de déploiement accidentel
- 🧪 **Tests**: Validation complète avant production
- 🎯 **Contrôle**: Déploiement seulement quand prêt
- 🔄 **Rollback**: Retour arrière facile si problème

---

## ⚠️ À NE PAS OUBLIER

**Sur le dashboard Render :**
1. Settings → Auto-Deploy → **NO**
2. Build & Deploy → Branch → **main**
3. Save Changes

**Date de configuration**: 13/01/2026 22:38
