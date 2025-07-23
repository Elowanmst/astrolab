# Configuration des Emails - Guide Astrolab

## 📧 Aperçu

Votre système Astrolab envoie automatiquement 2 types d'emails après chaque commande :
1. **Email de confirmation** au client (récapitulatif de commande)
2. **Email de notification** à l'admin (nouvelle commande à traiter)

**Mode actuel :** `LOG` (emails sauvés dans `storage/logs/laravel.log`)

---

## ⚙️ Configuration Email

### Mode actuel (LOG) - Pour les tests
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@astrolab.com"
MAIL_FROM_NAME="Astrolab"
MAIL_ADMIN_EMAIL="admin@astrolab.com"
```

Les emails sont écrits dans `storage/logs/laravel.log` au lieu d'être envoyés.

---

## 🚀 Fournisseurs Email Recommandés

### 1. **Brevo (ex-Sendinblue)** - Français 🇫🇷
✅ **Recommandé pour Astrolab**
- 300 emails/jour GRATUIT
- Interface en français
- Excellent support client
- Conformité RGPD

**Configuration `.env` :**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@astrolab.com
MAIL_PASSWORD=votre-cle-api-brevo
MAIL_ENCRYPTION=tls
```

**Inscription :** https://www.brevo.com

---

### 2. **Mailgun** - International
- 100 emails/jour GRATUIT
- API puissante
- Très fiable

**Configuration `.env` :**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.astrolab.com
MAILGUN_SECRET=key-xxxxxxxxxx
```

---

### 3. **Amazon SES** - Professionnel
- $0.10 pour 1000 emails
- Très économique à grande échelle
- Infrastructure AWS

**Configuration `.env` :**
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=votre-access-key
AWS_SECRET_ACCESS_KEY=votre-secret-key
AWS_DEFAULT_REGION=eu-west-1
```

---

### 4. **Gmail SMTP** - Simple pour commencer
⚠️ **Limité à 500 emails/jour**

**Configuration `.env` :**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=mot-de-passe-application
MAIL_ENCRYPTION=tls
```

---

## 🛠️ Activation Étape par Étape

### Étape 1 : Choisir le fournisseur
Nous recommandons **Brevo** pour commencer.

### Étape 2 : Créer un compte
1. Inscrivez-vous sur https://www.brevo.com
2. Vérifiez votre email
3. Récupérez votre clé API SMTP

### Étape 3 : Configurer le DNS (optionnel)
Pour améliorer la délivrabilité, ajoutez ces enregistrements DNS :

**SPF :**
```
TXT @ "v=spf1 include:spf.brevo.com ~all"
```

**DKIM :** (fourni par Brevo)
```
TXT brevo._domainkey "v=DKIM1; k=rsa; p=..."
```

### Étape 4 : Modifier le `.env`
```env
# Remplacer ces lignes
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@astrolab.com
MAIL_PASSWORD=votre-cle-api-brevo
MAIL_ENCRYPTION=tls
```

### Étape 5 : Tester
```bash
php artisan test:order-email
```

---

## 🧪 Tests et Débogage

### Tester l'envoi d'emails
```bash
# Tester avec la dernière commande
php artisan test:order-email

# Tester avec une commande spécifique
php artisan test:order-email 123
```

### Vérifier les logs
```bash
tail -f storage/logs/laravel.log
```

### Traiter la queue d'emails
```bash
# Traiter une fois
php artisan queue:work --once

# Traiter en continu (pour production)
php artisan queue:work
```

---

## 📱 Contenu des Emails

### Email Client (OrderConfirmation)
- ✅ Design Astrolab (noir/blanc)
- ✅ Récapitulatif complet de la commande
- ✅ Informations de livraison
- ✅ Étapes suivantes
- ✅ Responsive mobile

### Email Admin (NewOrderNotification)
- ✅ Alerte nouvelle commande
- ✅ Informations client
- ✅ Articles commandés
- ✅ Lien vers Filament admin
- ✅ Actions rapides

---

## ⚡ Mode Production

### Configuration recommandée
```env
MAIL_MAILER=smtp
QUEUE_CONNECTION=database
```

### Supervisor pour la queue (serveur)
```ini
[program:astrolab-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/astrolab/artisan queue:work --sleep=3 --tries=3
directory=/path/to/astrolab
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/astrolab-queue.log
autostart=true
autorestart=true
```

---

## 🔒 Sécurité

- ✅ Emails en queue (non bloquants)
- ✅ Gestion d'erreurs (commande pas interrompue si email échoue)
- ✅ Logs des erreurs d'envoi
- ✅ Validation des adresses email
- ✅ Headers de sécurité

---

## 📊 Suivi

### Métriques importantes
- Taux de délivrabilité
- Taux d'ouverture
- Emails en erreur
- Performance de la queue

### Monitoring recommandé
```bash
# Vérifier la queue
php artisan queue:monitor

# Statistiques
php artisan queue:stats
```

---

## 📞 Support

**Brevo Support :** https://help.brevo.com
**Laravel Mail :** https://laravel.com/docs/mail

**Commandes utiles :**
```bash
# Vider la queue
php artisan queue:clear

# Redémarrer les workers
php artisan queue:restart

# Voir les jobs échoués
php artisan queue:failed
```

---

*Les emails sont envoyés automatiquement après chaque commande confirmée. En mode LOG, ils sont sauvés dans les logs pour les tests.*
