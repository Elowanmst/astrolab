# 💳 Configuration Stripe pour Astrolab

## 🎯 Objectif
Configurer Stripe pour recevoir les paiements de vos clients et transférer l'argent sur votre compte bancaire automatiquement.

## 📋 Étapes de configuration

### 1. Création du compte Stripe

#### A. Inscription
1. Allez sur [stripe.com](https://stripe.com)
2. Cliquez sur "Commencer"
3. Créez un compte avec votre email professionnel

#### B. Vérification d'identité
Stripe va vous demander :
- **Pièce d'identité** : Passeport ou carte d'identité
- **Justificatif de domicile** : Facture récente (électricité, gaz, etc.)
- **Informations entreprise** : SIRET, adresse, secteur d'activité
- **Coordonnées bancaires** : IBAN de votre compte professionnel

⚠️ **Important** : Cette vérification peut prendre 1-3 jours ouvrés.

### 2. Configuration du compte bancaire

#### Dans votre dashboard Stripe :
1. **Paramètres** → **Payouts** → **Bank accounts**
2. Ajoutez votre **IBAN** et **BIC**
3. Stripe fera un micro-virement de validation
4. Confirmez le montant reçu

#### Calendrier des virements :
- **Quotidien** : Virement tous les jours ouvrés (recommandé)
- **Hebdomadaire** : Virement chaque vendredi
- **Mensuel** : Virement le dernier jour du mois

### 3. Récupération des clés API

#### Clés de test (développement) :
1. Dashboard Stripe → **Développeurs** → **Clés API**
2. Copiez :
   - `pk_test_...` (Clé publiable)
   - `sk_test_...` (Clé secrète)

#### Clés live (production) :
⚠️ **À faire uniquement après validation complète du site**
1. Activez le mode "Live" dans Stripe
2. Copiez les nouvelles clés live

### 4. Configuration dans votre application

#### A. Variables d'environnement (.env)
```env
# DÉVELOPPEMENT (clés de test)
STRIPE_PUBLISHABLE_KEY=pk_test_VOTRE_CLE_PUBLIABLE
STRIPE_SECRET_KEY=sk_test_VOTRE_CLE_SECRETE
STRIPE_WEBHOOK_SECRET=whsec_VOTRE_SECRET_WEBHOOK
STRIPE_CURRENCY=eur

# PRODUCTION (à remplacer par les clés live)
# STRIPE_PUBLISHABLE_KEY=pk_live_VOTRE_CLE_PUBLIABLE_LIVE
# STRIPE_SECRET_KEY=sk_live_VOTRE_CLE_SECRETE_LIVE
```

#### B. Configuration des webhooks
1. Dashboard Stripe → **Développeurs** → **Webhooks**
2. Cliquez sur **+ Ajouter un point de terminaison**
3. URL : `https://votre-domaine.com/webhook/stripe`
4. Événements à écouter :
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payout.paid`
   - `payout.failed`
5. Copiez le "Secret de signature" dans STRIPE_WEBHOOK_SECRET

### 5. Tests de paiement

#### Numéros de carte de test :
```
✅ Succès : 4242 4242 4242 4242
❌ Échec : 4000 0000 0000 0002
🔄 3D Secure : 4000 0000 0000 3220
```

#### Autres infos de test :
- **Date d'expiration** : N'importe quelle date future (ex: 12/28)
- **CVV** : N'importe quel code 3 chiffres (ex: 123)
- **Nom** : N'importe quel nom

### 6. Frais et commissions

#### Frais Stripe (Europe) :
- **Cartes européennes** : 1,4% + 0,25€ par transaction
- **Cartes non-européennes** : 2,9% + 0,25€ par transaction
- **Remboursements** : 0,25€ par remboursement
- **Contestations** : 15€ par contestation

#### Calcul exemple :
```
Commande de 50€ :
- Frais Stripe : (50 × 1,4%) + 0,25€ = 0,70€ + 0,25€ = 0,95€
- Vous recevez : 50€ - 0,95€ = 49,05€
```

### 7. Suivi et gestion

#### Dashboard Stripe - Sections importantes :
1. **Accueil** : Vue d'ensemble des ventes
2. **Paiements** : Liste de toutes les transactions
3. **Clients** : Base de données clients
4. **Payouts** : Historique des virements vers votre banque
5. **Rapports** : Analyses et statistiques

#### Notifications automatiques :
- Email à chaque virement vers votre banque
- Alertes pour les paiements échoués
- Rapports mensuels de revenus

### 8. Passage en production

#### Checklist avant activation :
- [ ] Compte Stripe vérifié et approuvé
- [ ] Compte bancaire validé
- [ ] Tests de paiement réussis
- [ ] Webhooks configurés et testés
- [ ] Conditions générales de vente en ligne
- [ ] Mentions légales à jour
- [ ] Politique de remboursement définie

#### Activation du mode live :
1. Remplacez les clés de test par les clés live dans `.env`
2. Mettez à jour l'URL du webhook avec votre domaine final
3. Testez avec un petit paiement réel

### 9. Sécurité et conformité

#### Mesures de sécurité :
- Les données de carte ne transitent jamais par vos serveurs
- Stripe est certifié PCI DSS niveau 1
- Chiffrement des données en transit et au repos
- Webhooks signés pour éviter les fraudes

#### Conformité RGPD :
- Stripe est conforme RGPD
- Données stockées en Europe (si configuré)
- Droit à l'effacement respecté

### 10. Support et ressources

#### En cas de problème :
1. **Documentation Stripe** : [docs.stripe.com](https://docs.stripe.com)
2. **Support Stripe** : Via le dashboard (chat en direct)
3. **Logs Laravel** : `storage/logs/laravel.log`
4. **Webhook logs** : Dashboard Stripe → Développeurs → Webhooks

#### Communauté :
- Discord Stripe France
- Stack Overflow (tag `stripe-payments`)
- Documentation officielle Laravel Cashier

---

## 💰 Résumé du flux d'argent

1. **Client paie** → Argent va chez Stripe
2. **Stripe prélève ses frais** (1,4% + 0,25€)
3. **Stripe vire le reste** sur votre compte bancaire (sous 2 jours)
4. **Vous recevez l'argent** sur votre compte professionnel

🎉 **Félicitations !** Votre système de paiement est maintenant configuré pour recevoir l'argent automatiquement !
