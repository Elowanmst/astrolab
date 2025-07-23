# Guide d'Administration - Livraisons et Paiements

## 🚚 Gestion des Livraisons

### Accès
Dans votre admin Filament : **Configuration > Méthodes de livraison**

### Fonctionnalités

#### 📋 **Liste des méthodes**
- Voir toutes les méthodes de livraison
- Filtrer par statut (actif/inactif)
- Réordonner par glisser-déposer
- Rechercher par nom ou code

#### ➕ **Ajouter une méthode**
1. Cliquez sur "Créer"
2. Remplissez les informations :
   - **Nom** : Ex. "Livraison Express"
   - **Code** : Ex. "express" (unique, sans espaces)
   - **Description** : Explication pour les clients
   - **Prix** : Coût de la livraison
   - **Gratuit dès** : Montant panier pour livraison gratuite
   - **Délais** : Estimation en jours
   - **Ordre** : Position dans la liste

#### ✏️ **Modifier une méthode**
- Cliquez sur l'icône crayon
- Modifiez les paramètres
- Sauvegardez

#### 🗑️ **Supprimer une méthode**
- Cliquez sur l'icône poubelle
- Confirmez la suppression

### 📦 **Méthodes par défaut créées**

1. **Retrait en magasin** (`pickup`)
   - Gratuit
   - 1-2 jours
   - Ordre 0 (premier)

2. **Livraison à domicile** (`home`)
   - 4,90€ (gratuit dès 50€)
   - 2-4 jours
   - Ordre 1

3. **Livraison express** (`express`)
   - 9,90€ (gratuit dès 100€)
   - 1 jour
   - Ordre 2

4. **Point relais** (`relay`)
   - 3,50€ (gratuit dès 75€)
   - 3-5 jours
   - Ordre 3

---

## 💳 Gestion des Paiements

### Accès
Dans votre admin Filament : **Configuration > Configuration Paiement**

### Fonctionnalités

#### 📋 **Liste des processeurs**
- Voir tous les processeurs configurés
- Filtrer par type ou statut
- Voir les frais de commission

#### ⚙️ **Configurer un processeur**

##### **Stripe** (Recommandé)
1. Créez un compte sur https://stripe.com
2. Dans l'admin, éditez la configuration Stripe
3. Ajoutez vos clés :
   ```
   public_key: pk_test_... (ou pk_live_...)
   secret_key: sk_test_... (ou sk_live_...)
   webhook_secret: whsec_...
   ```
4. Activez la configuration
5. Désactivez le mode test pour la production

##### **PayPal**
1. Créez un compte PayPal Business
2. Dans l'admin, éditez la configuration PayPal
3. Ajoutez vos clés :
   ```
   client_id: Votre Client ID
   client_secret: Votre Client Secret
   sandbox: true (test) ou false (production)
   ```

##### **Lyra/PayZen** (Français)
1. Créez un compte sur Lyra Collect
2. Dans l'admin, éditez la configuration Lyra
3. Ajoutez vos clés :
   ```
   shop_id: Votre identifiant boutique
   key_test: Clé de test
   key_prod: Clé de production
   endpoint: https://api.payzen.eu
   ```

### 🔄 **Activation d'un processeur**

⚠️ **Important** : Un seul processeur peut être actif à la fois.

1. Configurez d'abord tous les paramètres
2. **Testez** en mode test
3. Activez le processeur (les autres se désactivent automatiquement)
4. Pour la production : désactivez "Mode test"

### 💰 **Configuration des frais**

Chaque processeur a ses frais :
- **Commission (%)** : Pourcentage sur chaque vente
- **Frais fixes (€)** : Montant fixe par transaction

**Frais par défaut :**
- Stripe : 2,90% + 0,30€
- PayPal : 3,40% + 0,35€
- Lyra : 2,50% + 0,25€

### 🔒 **Sécurité**

- Les clés secrètes sont chiffrées en base
- Utilisez toujours le mode test d'abord
- Ne partagez jamais vos clés secrètes
- Configurez les webhooks pour la synchronisation

---

## 🎯 **Workflow recommandé**

### Pour les livraisons :
1. Configurez vos vraies méthodes de livraison
2. Désactivez celles que vous n'utilisez pas
3. Ajustez les prix selon vos tarifs
4. Testez une commande complète

### Pour les paiements :
1. Commencez par Stripe (plus simple)
2. Configurez en mode test
3. Testez avec les cartes de test
4. Vérifiez que l'argent arrive bien
5. Passez en mode production

### Cartes de test Stripe :
- **Succès** : 4242 4242 4242 4242
- **Échec** : 4000 0000 0000 0002
- **CVV** : 123, **Date** : n'importe quelle date future

---

## 📊 **Monitoring**

### Métriques à surveiller :
- Taux de conversion des commandes
- Méthodes de livraison les plus utilisées
- Frais de transaction
- Échecs de paiement

### Logs importantes :
- `storage/logs/laravel.log` : Erreurs générales
- Admin Filament : Statuts des commandes
- Dashboard du processeur : Transactions

---

## 🆘 **Dépannage**

### Problèmes courants :

**Livraison :**
- Méthode n'apparaît pas → Vérifiez qu'elle est active
- Prix incorrect → Vérifiez les seuils de gratuité
- Ordre incorrect → Ajustez le champ "Ordre d'affichage"

**Paiement :**
- Paiement refusé → Vérifiez les clés API
- Webhook échoue → Configurez l'URL webhook
- Mode test/prod → Vérifiez le flag "Mode test"

### Support :
- **Stripe** : https://support.stripe.com
- **PayPal** : https://developer.paypal.com
- **Lyra** : support@lyra.com

---

*Toutes les modifications sont prises en compte immédiatement sur le site.*
