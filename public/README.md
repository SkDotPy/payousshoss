# 🐾 Paw Connect - Plateforme d'Adoption d'Animaux

**Tagline :** *Agir ensemble, les protéger*

## 📋 Table des matières

- [À propos](#à-propos)
- [Fonctionnalités](#fonctionnalités)
- [Technologies](#technologies)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [API Routes](#api-routes)
- [Équipe](#équipe)

---

## 🎯 À propos

Paw Connect est une plateforme web permettant de faciliter l'adoption d'animaux abandonnés en mettant en relation adoptants, refuges et associations. Le projet inclut :

- Un **front-office** pour les utilisateurs
- Un **back-office** pour les administrateurs
- Un système de **recherche avancée** avec filtres
- Une **gestion complète** des utilisateurs et animaux
- Un système de **newsletter**
- Des **logs d'activité** détaillés

---

## ✨ Fonctionnalités

### Front-Office
- ✅ Inscription / Connexion avec captcha personnalisé
- ✅ Recherche d'animaux en temps réel (Fetch API)
- ✅ Filtres avancés (espèce, âge, race, couleur, sexe)
- ✅ Profil utilisateur avec avatar personnalisable (8 couleurs)
- ✅ Newsletter (inscription/désinscription)
- ✅ Mode Dark/Light persistant
- ✅ Design responsive (mobile, tablette, desktop)
- ✅ Animations fluides et transitions

### Back-Office
- ✅ Dashboard avec statistiques en temps réel
- ✅ Gestion des utilisateurs (CRUD complet)
- ✅ Gestion des animaux
- ✅ Gestion des adoptions
- ✅ Logs système avec filtres
- ✅ Gestion des questions captcha
- ✅ Sidebar collapsible
- ✅ Utilisateurs connectés en temps réel

### Sécurité
- ✅ Protection contre les injections SQL (PDO)
- ✅ Validation des données côté client et serveur
- ✅ Captcha personnalisé (pas de Google reCAPTCHA)
- ✅ Hachage des mots de passe
- ✅ Sessions sécurisées
- ✅ Auto-déconnexion après inactivité (30 min)
- ✅ Logs d'activité

---

## 🛠️ Technologies

### Front-End
- **HTML5** / **CSS3**
- **Bootstrap 5.3.2**
- **JavaScript ES6+** (Vanilla, pas de frameworks)
- **Font Awesome 6.4.0**
- **Google Fonts** (Poppins)

### Back-End
- **PHP 8.x** (natif, sans framework)
- **MySQL / MariaDB**
- **PDO** pour les requêtes

### Outils
- **Git / GitHub** (versionning)
- **Google Drive** (partage fichiers)
- **MAMP** (environnement local)
- **OVH VPS** (hébergement production)

---

## 📦 Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache)
- Git

### Étape 1 : Cloner le projet

```bash
git clone https://github.com/votre-equipe/paw-connect.git
cd paw-connect
```

### Étape 2 : Configuration de la base de données

1. Créer la base de données :

```sql
CREATE DATABASE pawconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importer le schéma :

```bash
mysql -u root -p pawconnect < database/schema.sql
```

3. Importer les données de test (optionnel) :

```bash
mysql -u root -p pawconnect < database/seed.sql
```

### Étape 3 : Configuration

1. Copier le fichier de configuration :

```bash
cp backend/config/database.example.php backend/config/database.php
```

2. Éditer `backend/config/database.php` :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pawconnect');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
?>
```

### Étape 4 : Permissions

```bash
chmod -R 755 public/
chmod -R 777 public/uploads/
```

### Étape 5 : Lancer le serveur

#### Avec MAMP
- Placer le projet dans `/Applications/MAMP/htdocs/paw-connect`
- Accéder à `http://localhost:8888/paw-connect`

#### Avec PHP Built-in Server
```bash
cd public
php -S localhost:8000
```

Accéder à `http://localhost:8000`

---

## 📁 Structure du projet

```
paw-connect/
│
├── public/                     # Front-end accessible
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css       # Styles principaux
│   │   │   └── admin.css      # Styles admin
│   │   ├── js/
│   │   │   ├── app.js         # JavaScript principal
│   │   │   ├── search.js      # Recherche fetch
│   │   │   └── avatar.js      # Gestion avatars
│   │   └── images/
│   │       └── animals/       # Photos animaux
│   │
│   ├── admin/                  # Back-office
│   │   ├── index.php          # Dashboard
│   │   ├── users.php          # Gestion utilisateurs
│   │   ├── animals.php        # Gestion animaux
│   │   └── logs.php           # Logs système
│   │
│   ├── index.php               # Page d'accueil
│   ├── login.php               # Connexion/Inscription
│   ├── profil.php              # Profil utilisateur
│   ├── search.php              # Recherche animaux
│   ├── newsletter.php          # Newsletter
│   └── animal-detail.php       # Détail animal
│
├── backend/                    # API & Logique métier
│   ├── config/
│   │   └── database.php       # Configuration BDD
│   ├── auth/
│   │   ├── login.php          # API connexion
│   │   └── register.php       # API inscription
│   ├── search/
│   │   └── animals.php        # API recherche
│   ├── newsletter/
│   │   ├── subscribe.php
│   │   └── unsubscribe.php
│   ├── admin/
│   │   ├── get-stats.php
│   │   └── get-logs.php
│   └── user/
│       ├── update-info.php
│       └── update-avatar.php
│
├── database/
│   ├── schema.sql              # Structure BDD
│   └── seed.sql                # Données de test
│
├── docs/
│   ├── cahier-des-charges.pdf
│   ├── charte-graphique.pdf
│   └── maquettes/
│
├── .gitignore
├── README.md
└── equipe.xlsx
```

---

## ⚙️ Configuration

### Variables d'environnement

Créer un fichier `backend/config/config.php` :

```php
<?php
// Configuration générale
define('SITE_NAME', 'Paw Connect');
define('SITE_URL', 'http://51.178.44.228');

// Email
define('MAIL_FROM', 'noreply@pawconnect.fr');
define('MAIL_FROM_NAME', 'Paw Connect');

// Session
define('SESSION_LIFETIME', 1800); // 30 minutes

// Upload
define('UPLOAD_MAX_SIZE', 5242880); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
?>
```

### Configuration Apache (.htaccess)

Créer `/public/.htaccess` :

```apache
# Réécriture d'URL
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Sécurité
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Gestion erreurs
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 🚀 Utilisation

### Compte Admin par défaut

```
Email: admin@pawconnect.fr
Mot de passe: Admin@2025
```

**⚠️ IMPORTANT : Changer ce mot de passe en production !**

### Créer un compte utilisateur

1. Aller sur `login.php`
2. Cliquer sur "Inscription"
3. Remplir le formulaire
4. Répondre au captcha
5. Valider l'email de confirmation

### Recherche

| Méthode | Route | Description | Paramètres |
|---------|-------|-------------|------------|
| GET | `/backend/search/animals.php` | Recherche animaux | `q, species, sex, age, race, color, sort` |

**Exemple :**
```javascript
fetch('/backend/search/animals.php?q=labrador&species=chien&age=1-3')
  .then(res => res.json())
  .then(data => console.log(data.animals));
```

### Utilisateur

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/backend/user/get-profile.php` | Récupérer profil |
| POST | `/backend/user/update-info.php` | Modifier infos |
| POST | `/backend/user/update-avatar.php` | Changer avatar |
| POST | `/backend/user/update-password.php` | Changer mot de passe |
| DELETE | `/backend/user/delete-account.php` | Supprimer compte |

### Newsletter

| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/backend/newsletter/subscribe.php` | S'abonner |
| POST | `/backend/newsletter/unsubscribe.php` | Se désabonner |

### Admin

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/backend/admin/get-stats.php` | Statistiques dashboard |
| GET | `/backend/admin/get-logs.php` | Liste des logs |
| GET | `/backend/admin/export-users.php` | Export utilisateurs CSV |
| DELETE | `/backend/admin/delete-user.php` | Supprimer utilisateur |

---

## 🎨 Charte Graphique

### Couleurs

```css
/* Primaire */
--primary: #1E3A8A;        /* Bleu institutionnel */
--primary-light: #60A5FA;  /* Bleu clair */

/* Secondaire */
--secondary: #10B981;      /* Vert tendre */

/* Neutres */
--dark: #374151;           /* Texte principal */
--gray: #9CA3AF;           /* Texte secondaire */
--light-gray: #F3F4F6;     /* Arrière-plans */
--white: #FFFFFF;          /* Fond principal */
```

### Typographie

```css
font-family: 'Poppins', sans-serif;

/* Poids disponibles */
font-weight: 400;  /* Regular */
font-weight: 500;  /* Medium */
font-weight: 600;  /* Semi-Bold */
font-weight: 700;  /* Bold */
```

### Logo

Le logo est composé :
- Icône patte (Font Awesome)
- Texte "Paw Connect" en Poppins Bold
- Couleur : Bleu institutionnel (#1E3A8A)

---

## 🧪 Tests

### Tests manuels

1. **Inscription/Connexion**
   - Créer un compte
   - Se connecter
   - Vérifier la session
   - Se déconnecter

2. **Recherche**
   - Recherche sans filtres
   - Recherche avec un filtre
   - Recherche avec plusieurs filtres
   - Vider la recherche

3. **Profil**
   - Modifier les infos
   - Changer l'avatar
   - Changer le mot de passe

4. **Dark Mode**
   - Activer le dark mode
   - Recharger la page
   - Vérifier la persistance

5. **Responsive**
   - Tester sur mobile (< 768px)
   - Tester sur tablette (768-1024px)
   - Tester sur desktop (> 1024px)

### Checklist de sécurité

- [ ] Toutes les requêtes SQL utilisent PDO avec prepared statements
- [ ] Les mots de passe sont hashés (password_hash)
- [ ] Les inputs utilisateurs sont validés et nettoyés
- [ ] Les sessions sont sécurisées (httponly, secure)
- [ ] HTTPS est activé en production
- [ ] Les erreurs SQL ne sont pas affichées à l'utilisateur
- [ ] Le .htaccess protège les fichiers sensibles
- [ ] Les uploads de fichiers sont validés
- [ ] Le site est protégé contre XSS et CSRF

---

## 📊 Base de Données

### Tables principales

#### `utilisateur`
```sql
CREATE TABLE utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    age INT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    actif BOOLEAN DEFAULT 1,
    security VARCHAR(50),
    Role VARCHAR(50) DEFAULT 'user',
    date_logout DATETIME,
    DateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
    nbAdoptions INT DEFAULT 0,
    nbAccueils INT DEFAULT 0,
    avatar_color VARCHAR(7) DEFAULT '#1E3A8A'
);
```

#### `animal`
```sql
CREATE TABLE animal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    IDuser VARCHAR(50),
    IDRefuge INT,
    nom VARCHAR(100),
    age INT,
    description TEXT,
    species VARCHAR(50),
    race VARCHAR(100),
    image VARCHAR(255),
    state VARCHAR(50) DEFAULT 'disponible',
    IMGnom VARCHAR(255),
    IMGtype VARCHAR(50),
    situation TEXT,
    adoption BOOLEAN DEFAULT 0,
    favori BOOLEAN DEFAULT 0,
    signatureRefuge VARCHAR(255),
    signatureUtilisateur VARCHAR(255),
    CarteIdentite VARCHAR(255),
    sex VARCHAR(10),
    color VARCHAR(50),
    FOREIGN KEY (IDRefuge) REFERENCES refuge(id)
);
```

#### `refuge`
```sql
CREATE TABLE refuge (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    description TEXT,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    CodePostal VARCHAR(10),
    email VARCHAR(255),
    password VARCHAR(255),
    actif BOOLEAN DEFAULT 1,
    security VARCHAR(50),
    Role VARCHAR(50) DEFAULT 'refuge',
    dateLogout DATETIME,
    DateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
    number VARCHAR(20),
    capacity INT,
    nbAdoptions INT DEFAULT 0,
    nbAcceil INT DEFAULT 0
);
```

#### `historique`
```sql
CREATE TABLE historique (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idUser INT,
    idRefuge INT,
    changIn TEXT,
    temps DATETIME DEFAULT CURRENT_TIMESTAMP,
    type VARCHAR(50),
    FOREIGN KEY (idUser) REFERENCES utilisateur(id),
    FOREIGN KEY (idRefuge) REFERENCES refuge(id)
);
```

#### `message`
```sql
CREATE TABLE message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idUser INT,
    idRefuge INT,
    message TEXT,
    nom VARCHAR(100),
    messagerie VARCHAR(255),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    chat VARCHAR(255),
    FOREIGN KEY (idUser) REFERENCES utilisateur(id),
    FOREIGN KEY (idRefuge) REFERENCES refuge(id)
);
```

---

## 🚢 Déploiement

### Sur OVH VPS

1. **Connexion SSH**
```bash
ssh root@51.178.44.228
```

2. **Installation des dépendances**
```bash
apt update
apt install apache2 php8.1 mysql-server php8.1-mysql
apt install fail2ban
```

3. **Configuration Apache**
```bash
nano /etc/apache2/sites-available/pawconnect.conf
```

```apache
<VirtualHost *:80>
    ServerName pawconnect.fr
    DocumentRoot /var/www/pawconnect/public
    
    <Directory /var/www/pawconnect/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/pawconnect_error.log
    CustomLog ${APACHE_LOG_DIR}/pawconnect_access.log combined
</VirtualHost>
```

4. **Activer le site**
```bash
a2ensite pawconnect
a2enmod rewrite
systemctl reload apache2
```

5. **Installer SSL (Let's Encrypt)**
```bash
apt install certbot python3-certbot-apache
certbot --apache -d pawconnect.fr
```

6. **Configurer Fail2Ban**
```bash
nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
```

7. **Déployer le code**
```bash
cd /var/www
git clone https://github.com/votre-equipe/paw-connect.git pawconnect
cd pawconnect
chmod -R 755 public/
chmod -R 777 public/uploads/
```

8. **Configurer la BDD**
```bash
mysql -u root -p
CREATE DATABASE pawconnect;
USE pawconnect;
SOURCE database/schema.sql;
```

---

## 🐛 Dépannage

### Problème : "Cannot connect to database"

**Solution :**
1. Vérifier les identifiants dans `backend/config/database.php`
2. Vérifier que MySQL est démarré : `sudo systemctl status mysql`
3. Vérifier les permissions de l'utilisateur MySQL

### Problème : "404 Not Found"

**Solution :**
1. Vérifier que `mod_rewrite` est activé : `a2enmod rewrite`
2. Vérifier le fichier `.htaccess`
3. Vérifier la configuration Apache (AllowOverride All)

### Problème : Le dark mode ne persiste pas

**Solution :**
1. Vérifier que localStorage est disponible dans le navigateur
2. Vérifier la console JavaScript pour les erreurs
3. Vider le cache du navigateur

### Problème : La recherche ne fonctionne pas

**Solution :**
1. Ouvrir la console réseau (F12)
2. Vérifier que la requête fetch atteint le serveur
3. Vérifier la réponse JSON de l'API
4. Vérifier les logs PHP : `tail -f /var/log/apache2/error.log`

---

## 👥 Équipe

| Membre | Rôle | Email |
|--------|------|-------|
| Votre Nom | Front-End / Design | votre.email@myges.fr |
| Coéquipier 1 | Back-End | coequipier1@myges.fr |
| Coéquipier 2 | Base de données | coequipier2@myges.fr |

**Chef de projet :** [Nom]

**Lien GitHub :** https://github.com/votre-equipe/paw-connect

**Serveur OVH :** http://51.178.44.228

---

## 📝 Licence

Ce projet est réalisé dans le cadre du **Projet Annuel 1AJ1** à l'ESGI.

© 2025 Équipe Paw Connect. Tous droits réservés.

---

## 📞 Support

Pour toute question ou problème :
- Email : contact@pawconnect.fr
- Issues GitHub : https://github.com/votre-equipe/paw-connect/issues

---

## 🙏 Remerciements

- **Enseignant** : Maxime Antoine (mantoine12@myges.fr)
- **Refuges partenaires** pour les tests
- **Communauté Open Source** pour les bibliothèques utilisées

---

**Fait avec ❤️ pour les animaux** 🐾cher un animal

1. Aller sur `search.php`
2. Utiliser la barre de recherche ou les filtres
3. Les résultats s'affichent en temps réel
4. Cliquer sur "Voir plus" pour les détails

### Adopter un animal

1. Se connecter
2. Voir la fiche de l'animal
3. Cliquer sur "Je veux adopter"
4. Remplir le formulaire d'adoption
5. Signer électroniquement
6. Télécharger le PDF

---

## 🔌 API Routes

### Authentification

| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/backend/auth/register.php` | Inscription |
| POST | `/backend/auth/login.php` | Connexion |
| POST | `/backend/auth/logout.php` | Déconnexion |

### Recher