# 📋 Document des Risques - Paw Connect

**Projet :** Paw Connect - Plateforme d'adoption d'animaux  
**Équipe :** [Noms des membres]  
**Date :** 09 Octobre 2025  
**Version :** 1.0

---

## 1. Risques Techniques

### 1.1 Sécurité & Vulnérabilités

#### 🔴 Risque : Injection SQL
**Probabilité :** Moyenne  
**Impact :** Critique  
**Description :** Un attaquant pourrait injecter du code SQL malveillant via les formulaires.

**Solutions mises en place :**
- ✅ Utilisation de **PDO avec prepared statements** pour toutes les requêtes
- ✅ Validation et nettoyage de tous les inputs utilisateurs
- ✅ Interdiction d'utilisation de mysqli (comme requis dans le syllabus)

**Code exemple :**
```php
// ❌ MAUVAIS (vulnérable)
$sql = "SELECT * FROM users WHERE email = '$email'";

// ✅ BON (sécurisé)
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email]);
```

---

#### 🟡 Risque : XSS (Cross-Site Scripting)
**Probabilité :** Moyenne  
**Impact :** Élevé  
**Description :** Injection de scripts malveillants dans les pages web.

**Solutions :**
- ✅ `htmlspecialchars()` sur tous les affichages de données utilisateur
- ✅ Validation des inputs côté client ET serveur
- ✅ Content Security Policy dans les headers

**Code exemple :**
```php
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

---

#### 🟡 Risque : CSRF (Cross-Site Request Forgery)
**Probabilité :** Faible  
**Impact :** Moyen  
**Description :** Un attaquant pourrait forcer un utilisateur authentifié à effectuer des actions non désirées.

**Solutions :**
- ✅ Tokens CSRF dans tous les formulaires critiques
- ✅ Vérification du `Referer` header
- ✅ SameSite cookies

---

#### 🔴 Risque : Mots de passe faibles
**Probabilité :** Élevée  
**Impact :** Critique  
**Description :** Les utilisateurs peuvent choisir des mots de passe trop simples.

**Solutions :**
- ✅ Validation minimum 8 caractères
- ✅ Indicateur de force du mot de passe en temps réel
- ✅ Hachage avec `password_hash()` (bcrypt)
- ✅ Jamais de stockage en clair

---

### 1.2 Performance & Disponibilité

#### 🟡 Risque : Charge serveur élevée
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** Trop de requêtes simultanées peuvent ralentir ou crasher le serveur.

**Solutions :**
- ✅ Mise en cache des résultats de recherche (côté client avec Map)
- ✅ Debounce sur les recherches (500ms)
- ✅ Limitation à 50 résultats par recherche
- ✅ Pagination des listes
- ⏳ À implémenter : Cache Redis côté serveur

---

#### 🟡 Risque : Panne de la base de données
**Probabilité :** Faible  
**Impact :** Critique  
**Description :** La BDD pourrait être indisponible.

**Solutions :**
- ✅ Gestion des erreurs PDO avec try/catch
- ✅ Messages d'erreur génériques pour l'utilisateur
- ✅ Logs des erreurs pour le debug
- ⏳ À implémenter : Backup automatique quotidien

---

#### 🟠 Risque : Timeout des sessions
**Probabilité :** Élevée  
**Impact :** Faible  
**Description :** Les utilisateurs peuvent être déconnectés pendant l'utilisation.

**Solutions :**
- ✅ Auto-déconnexion après 30 min d'inactivité
- ✅ Notification avant déconnexion
- ✅ Sauvegarde automatique des formulaires en cours (localStorage)

---

### 1.3 Compatibilité & Accessibilité

#### 🟡 Risque : Incompatibilité navigateurs
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** Le site pourrait ne pas fonctionner sur certains navigateurs.

**Solutions testées :**
- ✅ Chrome 100+ (OK)
- ✅ Firefox 95+ (OK)
- ✅ Safari 15+ (OK)
- ✅ Edge 100+ (OK)
- ❌ Internet Explorer (non supporté, comme prévu)

**Technologies utilisées :**
- Fetch API (supportée depuis 2015)
- CSS Grid & Flexbox (supportés)
- LocalStorage (supporté)

---

#### 🟢 Risque : Non-responsive
**Probabilité :** Faible  
**Impact :** Élevé  
**Description :** Le site pourrait être inutilisable sur mobile.

**Solutions :**
- ✅ Bootstrap 5 (mobile-first)
- ✅ Tests sur 3 tailles d'écran :
  - Mobile (< 768px)
  - Tablette (768-1024px)
  - Desktop (> 1024px)
- ✅ Menu hamburger sur mobile
- ✅ Fonctionnalité tablette : signature tactile (à implémenter)

---

## 2. Risques Fonctionnels

### 2.1 Gestion des données

#### 🟡 Risque : Perte de données utilisateur
**Probabilité :** Faible  
**Impact :** Critique  
**Description :** Suppression accidentelle ou corruption de données.

**Solutions :**
- ✅ Confirmation avant toute suppression
- ✅ Soft delete (champ `deleted_at` au lieu de DELETE)
- ✅ Logs de toutes les modifications
- ⏳ À implémenter : Backup automatique

---

#### 🟠 Risque : Upload de fichiers malveillants
**Probabilité :** Moyenne  
**Impact :** Élevé  
**Description :** Upload d'images contenant du code malveillant.

**Solutions :**
- ✅ Validation de l'extension (jpg, png, gif uniquement)
- ✅ Limite de taille (5 MB)
- ✅ Renommage des fichiers uploadés
- ✅ Stockage hors du DocumentRoot
- ⏳ À implémenter : Scan antivirus

---

#### 🟡 Risque : Données incohérentes
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** Données en BDD non synchronisées (ex: animal adopté mais marqué disponible).

**Solutions :**
- ✅ Contraintes foreign keys en BDD
- ✅ Transactions SQL pour les opérations critiques
- ✅ Validation des états avant modification
- ✅ Logs des changements d'état

---

### 2.2 Expérience utilisateur

#### 🟢 Risque : Captcha trop difficile
**Probabilité :** Faible  
**Impact :** Moyen  
**Description :** Les utilisateurs ne parviennent pas à s'inscrire à cause du captcha.

**Solutions :**
- ✅ Questions simples (ex: "Capitale de France ?")
- ✅ Gestion des erreurs avec nouvelle question
- ✅ Pas de limite de tentatives (mais avec rate limiting)
- ✅ Liste de questions variées dans le back-office

---

#### 🟡 Risque : Recherche lente
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** La recherche avec fetch prend trop de temps.

**Solutions :**
- ✅ Debounce de 500ms
- ✅ Cache des résultats (Map JavaScript)
- ✅ Limite de 50 résultats
- ✅ Index sur les colonnes recherchées en BDD
- ✅ Loading indicator pendant la recherche

---

#### 🟡 Risque : Dark mode non fonctionnel
**Probabilité :** Faible  
**Impact :** Faible  
**Description :** Le dark mode ne s'active pas ou ne persiste pas.

**Solutions :**
- ✅ LocalStorage pour persistance
- ✅ Fallback sur le thème clair si erreur
- ✅ Variables CSS pour tous les éléments
- ✅ Test sur toutes les pages

**Limitation connue :**
- ❌ Le localStorage ne fonctionne pas en navigation privée → Le thème n'est pas sauvegardé

---

## 3. Risques Organisationnels

### 3.1 Gestion de projet

#### 🟠 Risque : Retard dans le planning
**Probabilité :** Moyenne  
**Impact :** Élevé  
**Description :** Non-respect des deadlines des étapes.

**Solutions :**
- ✅ Répartition claire des tâches (front/back/BDD)
- ✅ Branches Git séparées (frontend/backend)
- ✅ Merge quotidien sur main après tests
- ✅ Suivi avec GLPI (gestion tickets)

**Deadlines critiques :**
- Étape 1 : 14/09/2025
- Étape 2 : 12/10/2025 ⚠️ (AUJOURD'HUI)
- Étape 3 : 16/11/2025
- Rendu final : 31/11/2025

---

#### 🟡 Risque : Conflits Git
**Probabilité :** Élevée  
**Impact :** Moyen  
**Description :** Conflits lors des merges entre branches.

**Solutions :**
- ✅ Branches séparées (frontend/backend)
- ✅ Merge uniquement après tests
- ✅ Convention de nommage des fichiers
- ✅ Communication dans l'équipe

---

#### 🟢 Risque : Perte du code source
**Probabilité :** Très faible  
**Impact :** Critique  
**Description :** Perte du code par accident.

**Solutions :**
- ✅ GitHub (repository distant)
- ✅ Google Drive (backup du zip)
- ✅ Serveur OVH (code en production)
- ✅ Machines locales de chaque membre

---

### 3.2 Communication

#### 🟡 Risque : Manque de communication
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** Incompréhension entre front et back.

**Solutions :**
- ✅ Documentation des routes API (README)
- ✅ Format JSON standardisé pour les réponses
- ✅ GLPI pour les tickets
- ✅ Google Drive pour partage de fichiers

**Format de réponse API standardisé :**
```json
{
  "success": true/false,
  "message": "Message descriptif",
  "data": {}
}
```

---

## 4. Risques Légaux & Conformité

### 4.1 RGPD

#### 🔴 Risque : Non-conformité RGPD
**Probabilité :** Élevée  
**Impact :** Critique  
**Description :** Non-respect de la protection des données personnelles.

**Solutions :**
- ✅ Consentement explicite (checkbox CGV)
- ✅ Possibilité de suppression de compte
- ✅ Pas de stockage de données sensibles non nécessaires
- ✅ Cookies limités (session + dark mode uniquement)
- ⏳ À ajouter : Page de politique de confidentialité

---

#### 🟡 Risque : Données sensibles non chiffrées
**Probabilité :** Faible  
**Impact :** Élevé  
**Description :** Transmission de données en clair.

**Solutions :**
- ✅ HTTPS en production (Let's Encrypt)
- ✅ Mots de passe hashés
- ✅ Pas de stockage de numéros de carte bancaire
- ✅ Sessions sécurisées (httponly, secure)

---

### 4.2 Propriété intellectuelle

#### 🟢 Risque : Utilisation d'images non libres
**Probabilité :** Faible  
**Impact :** Moyen  
**Description :** Utilisation d'images d'animaux sans droits.

**Solutions :**
- ✅ Photos de refuges partenaires (avec autorisation)
- ✅ Placeholders en phase de développement
- ✅ Attribution des sources
- ⏳ À faire : Contrat avec refuges

---

## 5. Risques Liés au Déploiement

### 5.1 Configuration serveur

#### 🟡 Risque : Serveur OVH mal configuré
**Probabilité :** Moyenne  
**Impact :** Élevé  
**Description :** Le site ne fonctionne pas en production.

**Solutions :**
- ✅ Tests en local d'abord (MAMP)
- ✅ Configuration Apache documentée
- ✅ Fail2ban configuré
- ✅ HTTPS avec certificat SSL
- ✅ Gestion des erreurs 404/500

---

#### 🟠 Risque : Attaque par force brute
**Probabilité :** Élevée  
**Impact :** Élevé  
**Description :** Tentatives de connexion automatisées.

**Solutions :**
- ✅ Fail2ban (ban après 5 tentatives)
- ✅ Logs de toutes les tentatives
- ✅ Captcha à l'inscription
- ⏳ À implémenter : Rate limiting sur /login.php

---

### 5.2 Maintenance

#### 🟡 Risque : Bugs en production non détectés
**Probabilité :** Moyenne  
**Impact :** Moyen  
**Description :** Des bugs apparaissent uniquement en production.

**Solutions :**
- ✅ Logs d'erreurs activés
- ✅ Monitoring des erreurs 500
- ✅ Tests avant chaque déploiement
- ✅ Rollback possible via Git

---

## 6. Améliorations Futures

### À implémenter après le rendu

1. **Sécurité :**
   - [ ] Authentification à deux facteurs (2FA)
   - [ ] Rate limiting sur toutes les routes
   - [ ] Scan antivirus des uploads

2. **Performance :**
   - [ ] Cache Redis
   - [ ] CDN pour les images
   - [ ] Compression Gzip

3. **Fonctionnalités :**
   - [ ] Paiement en ligne pour les dons
   - [ ] Messagerie en temps réel (WebSockets)
   - [ ] Application mobile (PWA)
   - [ ] Génération de contrats PDF automatique

4. **Monitoring :**
   - [ ] Dashboard de métriques (CPU, RAM, requêtes/s)
   - [ ] Alertes email en cas d'erreur
   - [ ] Backup automatique quotidien

---

## 7. Conclusion

### Risques critiques maîtrisés ✅
- Injection SQL → PDO
- Mots de passe → Hachage
- XSS → htmlspecialchars()
- HTTPS → Let's Encrypt

### Risques résiduels ⚠️
- Charge serveur élevée → Cache à implémenter
- Backup automatique → À configurer
- Rate limiting → À ajouter

### Points d'attention 🔍
- Tester intensivement avant la soutenance
- Vérifier tous les formulaires
- Valider la conformité RGPD
- Documenter toutes les fonctionnalités

---

**Document rédigé par :** [Votre nom]  
**Date de dernière mise à jour :** 09/10/2025  
**Version :** 1.0

---

**Note :** Ce document sera mis à jour tout au long du projet en fonction des nouveaux risques identifiés et des solutions apportées.