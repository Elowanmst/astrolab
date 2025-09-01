# 🚀 GUIDE DE CONFIGURATION PAIEMENT PRODUCTION

## 📋 CHECKLIST DE MISE EN PRODUCTION

### ✅ **1. Configuration Stripe**

#### Obtenir les clés Stripe :
1. **Créer un compte Stripe** : https://dashboard.stripe.com/register
2. **Activer votre compte** (vérification d'identité requise)
3. **Récupérer les clés** dans le dashboard Stripe :
   - `Développeurs` → `Clés API`
   - **Clé publique** : `pk_live_...` (commence par pk_live_)
   - **Clé secrète** : `sk_live_...` (commence par sk_live_)

#### Configurer le fichier .env :
```bash
# CONFIGURATION PAIEMENT
PAYMENT_PROCESSOR=stripe

# STRIPE PRODUCTION
STRIPE_PUBLISHABLE_KEY=pk_live_VOTRE_CLE_PUBLIQUE_STRIPE
STRIPE_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE_STRIPE
STRIPE_WEBHOOK_SECRET=whsec_VOTRE_SECRET_WEBHOOK_STRIPE
```

### ✅ **2. Configuration des Webhooks Stripe**

#### Créer un webhook :
1. **Aller dans le dashboard Stripe** → `Développeurs` → `Webhooks`
2. **Cliquer sur "Ajouter un endpoint"**
3. **URL du webhook** : `https://votre-domaine.com/webhook/stripe`
4. **Événements à écouter** :
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.dispute.created`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`

#### Récupérer le secret du webhook :
1. **Cliquer sur votre webhook créé**
2. **Copier le "Secret de signature"** : `whsec_...`
3. **L'ajouter dans votre .env**

### ✅ **3. Mise à jour du code**

#### Le code a été automatiquement préparé :
- ✅ **PaymentService** nettoyé (simulation supprimée)
- ✅ **Configuration** mise à jour (Stripe par défaut)
- ✅ **SDK Stripe** installé
- ✅ **Services** configurés

### ✅ **4. Sécurité et Tests**

#### Tests à effectuer AVANT la production :
```bash
# 1. Tester avec les clés de test Stripe d'abord
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...

# 2. Vérifier le processus complet :
# - Ajout au panier
# - Checkout
# - Paiement
# - Confirmation email
# - Admin notification
```

#### Cartes de test Stripe :
```
✅ Succès garanti:
4242 4242 4242 4242 (Visa)
5555 5555 5555 4444 (Mastercard)

❌ Échecs spécifiques:
4000 0000 0000 0002 (Carte déclinée)
4000 0000 0000 0127 (Carte expirée)
```

### ✅ **5. Variables d'environnement requises**

```bash
# .env PRODUCTION
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# PAIEMENT
PAYMENT_PROCESSOR=stripe
STRIPE_PUBLISHABLE_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# EMAIL (pour les confirmations)
MAIL_MAILER=smtp
MAIL_HOST=votre-serveur-smtp.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="Astrolab"

# QUEUE (pour les emails)
QUEUE_CONNECTION=database
```

### ✅ **6. Commandes de déploiement**

```bash
# Sur le serveur de production :
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan queue:work --daemon
```

### ✅ **7. Monitoring et Logs**

#### Vérifier les logs :
```bash
# Logs de paiement
tail -f storage/logs/laravel.log | grep "Paiement"

# Vérifier les webhooks Stripe
# Dashboard Stripe → Webhooks → Votre webhook → Onglet "Logs"
```

## 🔧 PERSONNALISATION AVANCÉE

### Modifier les frais Stripe :
```php
// Dans PaymentService.php, ligne ~56
'fees' => round($order->total_amount * 0.029 + 0.25, 2), // 2.9% + 0.25€
```

### Ajouter d'autres processeurs :
```php
// Dans PaymentService.php
case 'paypal':
    return $this->processPayPalPayment($paymentData, $order);
```

## 📞 SUPPORT

### En cas de problème :
1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Dashboard Stripe** : vérifier les paiements et webhooks
3. **Tester avec les clés de test** avant la production

### Contacts utiles :
- **Support Stripe** : https://support.stripe.com
- **Documentation Stripe** : https://stripe.com/docs

---

**⚠️ IMPORTANT :** 
- Toujours tester avec les clés de test avant la production
- Vérifier que HTTPS est activé sur votre domaine
- Sauvegarder votre base de données avant la mise en production
