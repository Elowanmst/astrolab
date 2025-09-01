# Documentation de Déploiement Production - Astrolab

## 🚀 Préparation de Production Complète

Cette documentation décrit le processus complet de déploiement en production de l'application Astrolab avec toutes les mesures de sécurité et d'optimisation implémentées.

## 📋 Checklist de Production

### ✅ Configuration Production
- [x] Configuration environnement (.env.production)
- [x] Configuration sécurité (config/security.php)
- [x] Optimisations Laravel (cache, config, routes)
- [x] Configuration base de données production
- [x] Configuration SSL/HTTPS obligatoire

### ✅ Sécurité Implémentée

#### Middleware de Sécurité
- [x] **SecurityHeaders** : CSP, HSTS, XSS Protection, Frame Options
- [x] **SecurityRateLimiting** : Rate limiting intelligent avec détection d'activité suspecte
- [x] **SecurityLogging** : Audit complet des requêtes sensibles
- [x] **WebhookSecurity** : Protection spécifique pour les webhooks Stripe

#### Validation et Protection
- [x] **SecurePaymentRequest** : Validation avancée avec algorithme de Luhn
- [x] **SecurityValidationService** : Détection de fraude et scoring de risque
- [x] **PaymentService** sécurisé : Intégration complète des vérifications de sécurité

### ✅ Scripts de Déploiement
- [x] **deploy-production.sh** : Déploiement automatisé avec rollback
- [x] **setup-server.sh** : Configuration complète du serveur Ubuntu/Debian

### ✅ Monitoring et Logging
- [x] Canaux de logging spécialisés (security, payments, system, performance)
- [x] Audit trail complet des transactions
- [x] Monitoring des tentatives de fraude
- [x] Logging des performances et erreurs

## 🔧 Installation et Déploiement

### 1. Préparation du Serveur

```bash
# Rendre le script exécutable
chmod +x scripts/setup-server.sh

# Exécuter la configuration serveur (Ubuntu/Debian)
sudo ./scripts/setup-server.sh your-domain.com your-email@example.com
```

Ce script configure automatiquement :
- PHP 8.3 et extensions requises
- MySQL 8.0
- Nginx avec configuration optimisée
- SSL/TLS avec Let's Encrypt
- Firewall (UFW)
- Fail2ban pour la protection DDoS
- Utilisateur de déploiement sécurisé

### 2. Configuration de l'Application

```bash
# Copier la configuration production
cp .env.production .env

# Éditer les variables d'environnement
nano .env
```

**Variables critiques à configurer :**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Base de données production
DB_HOST=localhost
DB_DATABASE=astrolab_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Stripe production
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Sécurité
SECURITY_RATE_LIMITING_ENABLED=true
SECURITY_SUSPICIOUS_THRESHOLD=10
SECURITY_BLOCK_DURATION=3600

# Mail production
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
```

### 3. Déploiement Automatisé

```bash
# Rendre le script exécutable
chmod +x scripts/deploy-production.sh

# Premier déploiement
./scripts/deploy-production.sh

# Déploiements suivants (avec sauvegarde automatique)
./scripts/deploy-production.sh
```

Le script de déploiement effectue automatiquement :
- Sauvegarde complète (base de données + fichiers)
- Mise en mode maintenance
- Pull des dernières modifications Git
- Installation des dépendances (Composer + NPM)
- Migrations de base de données
- Optimisations Laravel (cache config, routes, vues)
- Build des assets front-end
- Configuration des permissions
- Tests de santé de l'application
- Retour en ligne

### 4. Configuration SSL/HTTPS

Le script de setup configure automatiquement SSL avec Let's Encrypt. Pour renouveler :

```bash
# Renouvellement automatique (déjà configuré en cron)
certbot renew --quiet

# Test de renouvellement
certbot renew --dry-run
```

## 🛡️ Configuration de Sécurité

### Middleware de Sécurité

#### SecurityHeaders
Configure automatiquement :
- **Content Security Policy (CSP)** : Protection XSS avancée
- **HTTP Strict Transport Security (HSTS)** : Force HTTPS
- **X-Frame-Options** : Protection contre le clickjacking
- **X-Content-Type-Options** : Prévention du MIME sniffing
- **Referrer-Policy** : Contrôle des informations de référence

#### SecurityRateLimiting
- Rate limiting adaptatif par type de route
- Détection d'activité suspecte
- Blocage automatique des IP malveillantes
- Whitelist IP pour les utilisateurs de confiance

#### SecurityLogging
- Audit complet des requêtes sensibles
- Logging spécialisé par type d'activité
- Détection et alerte sur les tentatives de fraude
- Métadonnées complètes pour investigation

### Protection des Paiements

#### Validation Avancée
- **Algorithme de Luhn** : Validation mathématique des numéros de carte
- **Validation d'expiration** : Vérification des dates d'expiration
- **Détection de fraude** : Scoring de risque basé sur multiple critères
- **Rate limiting spécifique** : Protection contre les tentatives de force brute

#### Sécurité Stripe
- Validation des signatures webhook
- Métadonnées de sécurité ajoutées aux transactions
- Logging complet des événements de paiement
- Gestion sécurisée des erreurs

## 📊 Monitoring et Maintenance

### Logs de Sécurité

```bash
# Monitoring des tentatives de fraude
tail -f storage/logs/security.log

# Analyse des paiements
tail -f storage/logs/payments.log

# Monitoring système
tail -f storage/logs/laravel.log
```

### Commandes de Maintenance

```bash
# Nettoyage des logs (à exécuter régulièrement)
php artisan log:clear

# Nettoyage du cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimisation pour production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Monitoring des performances
php artisan queue:work --daemon
```

### Sauvegarde Automatisée

Le script de déploiement crée automatiquement des sauvegardes. Pour configurer des sauvegardes régulières :

```bash
# Ajouter au crontab
crontab -e

# Sauvegarde quotidienne à 2h du matin
0 2 * * * /path/to/backup-script.sh
```

## 🚨 Gestion des Urgences

### Rollback Rapide

En cas de problème après déploiement :

```bash
# Le script de déploiement crée automatiquement un point de restauration
# Les instructions de rollback sont affichées en fin de déploiement

# Restauration manuelle si nécessaire
git checkout previous-commit-hash
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback
```

### Mode Maintenance

```bash
# Activer le mode maintenance
php artisan down --refresh=15 --message="Maintenance en cours"

# Désactiver le mode maintenance
php artisan up
```

### Support et Investigation

En cas d'incident :

1. **Vérifier les logs** : `storage/logs/`
2. **Analyser la sécurité** : Logs de sécurité et tentatives d'intrusion
3. **Contrôler les paiements** : Vérifier les transactions Stripe et logs de paiement
4. **Performance** : Analyser les temps de réponse et la charge serveur

## 📈 Optimisations Performance

### Configuration Serveur
- **PHP OPcache** : Cache des opcodes activé
- **MySQL optimisé** : Configuration pour production
- **Nginx** : Compression gzip et cache statique
- **Redis** : Cache et sessions (optionnel)

### Optimisations Laravel
- **Cache config** : Configuration mise en cache
- **Cache routes** : Routes compilées
- **Cache vues** : Templates Blade compilés
- **Autoloader optimisé** : Classes pré-chargées

### Monitoring Continu
- **Logs de performance** : Temps de réponse et requêtes lentes
- **Monitoring base de données** : Requêtes optimisées
- **Cache hit ratio** : Efficacité du cache
- **Métriques serveur** : CPU, mémoire, disque

---

## ✅ Production Ready

Votre application Astrolab est maintenant prête pour la production avec :

- **Sécurité niveau entreprise** : Protection multi-couches contre les menaces
- **Déploiement automatisé** : Scripts de déploiement avec rollback
- **Monitoring complet** : Logs spécialisés et audit trail
- **Performance optimisée** : Configuration serveur et application optimisées
- **Conformité PCI** : Respect des standards de sécurité pour les paiements

La configuration implémentée respecte les meilleures pratiques de sécurité et de performance pour une application e-commerce en production.
