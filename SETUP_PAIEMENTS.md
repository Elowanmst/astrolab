# Configuration des Paiements - Guide d'Installation

## 📋 Aperçu

Votre système Astrolab est actuellement configuré en mode **simulation de paiement**. Aucun argent réel n'est traité. 

Pour activer les vrais paiements, suivez ce guide pour configurer un processeur de paiement.

---

## 🔧 Processeurs de Paiement Disponibles

### 1. **Stripe** (Recommandé - International)
- ✅ Le plus simple à intégrer
- ✅ Excellent support technique
- ✅ Interface développeur conviviale
- 💰 2,9% + 0,30€ par transaction

**Installation :**
```bash
composer require stripe/stripe-php
```

**Configuration dans `.env` :**
```env
PAYMENT_PROCESSOR=stripe
STRIPE_ENABLED=true
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

**Où obtenir les clés :**
1. Créez un compte sur https://stripe.com
2. Allez dans Dashboard > Developers > API keys
3. Copiez vos clés de test

---

### 2. **Lyra/PayZen** (Solution Française)
- ✅ Processeur français (Lyra Collect)
- ✅ Bon support en français
- ✅ Tarifs compétitifs
- 💰 2,5% + 0,25€ par transaction

**Installation :**
```bash
composer require lyracom/rest-php-sdk
```

**Configuration dans `.env` :**
```env
PAYMENT_PROCESSOR=lyra
LYRA_ENABLED=true
LYRA_SHOP_ID=votre_shop_id
LYRA_KEY_TEST=votre_cle_test
LYRA_KEY_PROD=votre_cle_prod
```

---

### 3. **PayPal** (International)
- ✅ Marque reconnue mondialement
- ✅ Accepte de nombreux modes de paiement
- 💰 3,4% + 0,35€ par transaction

**Installation :**
```bash
composer require paypal/rest-api-sdk-php
```

**Configuration dans `.env` :**
```env
PAYMENT_PROCESSOR=paypal
PAYPAL_ENABLED=true
PAYPAL_CLIENT_ID=votre_client_id
PAYPAL_CLIENT_SECRET=votre_client_secret
PAYPAL_SANDBOX=true  # false en production
```

---

## 💸 Où Va l'Argent ?

### Configuration des Comptes de Réception

**Stripe :**
- L'argent arrive sur votre compte Stripe
- Virement automatique sur votre compte bancaire (J+2 à J+7)
- Tableau de bord : https://dashboard.stripe.com

**Lyra/PayZen :**
- L'argent arrive sur votre compte Lyra Collect
- Virement sur votre compte bancaire selon fréquence choisie
- Back-office : https://secure.lyra.com

**PayPal :**
- L'argent arrive sur votre compte PayPal Business
- Virement manuel ou automatique vers votre banque

---

## 🚀 Activation Étape par Étape

### Étape 1 : Choisir votre Processeur
Nous recommandons **Stripe** pour commencer (plus simple).

### Étape 2 : Créer un Compte
1. Inscrivez-vous sur le site du processeur
2. Vérifiez votre identité et votre entreprise
3. Configurez votre compte bancaire

### Étape 3 : Installer le SDK
```bash
# Pour Stripe
composer require stripe/stripe-php

# Pour Lyra
composer require lyracom/rest-php-sdk

# Pour PayPal
composer require paypal/rest-api-sdk-php
```

### Étape 4 : Configuration
Ajoutez vos clés dans le fichier `.env` :

```env
# Remplacez 'simulation' par le processeur choisi
PAYMENT_PROCESSOR=stripe

# Ajoutez les clés de votre processeur
STRIPE_ENABLED=true
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
```

### Étape 5 : Test
1. Effectuez une commande test avec les numéros de carte de test
2. Vérifiez que la transaction apparaît dans votre tableau de bord
3. Testez un remboursement

### Étape 6 : Production
1. Obtenez vos clés de production
2. Changez `STRIPE_PUBLIC_KEY` et `STRIPE_SECRET_KEY` pour les clés live
3. Testez avec une vraie carte (montant minimal)

---

## 🧪 Numéros de Carte de Test

### Stripe
- **Succès :** 4242 4242 4242 4242
- **Échec :** 4000 0000 0000 0002
- **CVV :** 123
- **Date :** n'importe quelle date future

### Lyra/PayZen
- **Succès :** 4970 1000 0000 0003
- **Échec :** 4970 1000 0000 0011

---

## 📊 Suivi des Paiements

Votre admin Filament affiche déjà :
- ✅ Statut de paiement
- ✅ Méthode de paiement  
- ✅ ID de transaction
- ✅ Montant total

Accédez à votre admin : `votre-site.com/admin`

---

## 🔒 Sécurité

- ✅ Les données de carte ne sont JAMAIS stockées sur votre serveur
- ✅ Toutes les communications sont chiffrées (HTTPS/TLS)
- ✅ Conformité PCI DSS via les processeurs
- ✅ Webhooks pour la synchronisation des statuts

---

## 📞 Support

**Stripe :**
- Documentation : https://stripe.com/docs
- Support : Via le dashboard Stripe

**Lyra :**
- Documentation : https://docs.lyra.com
- Support : support@lyra.com

**PayPal :**
- Documentation : https://developer.paypal.com
- Support : Via le centre d'aide PayPal

---

## ⚠️ Important

1. **Commencez TOUJOURS en mode test**
2. **Vérifiez les lois locales** sur les paiements en ligne
3. **Configurez vos webhooks** pour la synchronisation
4. **Sauvegardez vos clés** dans un gestionnaire de mots de passe

---

*Pour toute question technique, consultez le code dans :*
- `app/Services/Payment/PaymentService.php`
- `config/payment.php`
- `app/Http/Controllers/CheckoutController.php`
