#!/bin/bash

# Script de configuration serveur pour Astrolab
# À exécuter sur un serveur Ubuntu/Debian fraîchement installé

set -e

echo "🔧 === CONFIGURATION SERVEUR ASTROLAB ==="

# Configuration
DOMAIN="votre-domaine.com"
APP_USER="astrolab"
APP_DIR="/var/www/astrolab"
PHP_VERSION="8.3"
MYSQL_ROOT_PASSWORD=""
APP_DB_PASSWORD=""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# Mise à jour système
update_system() {
    log_info "Mise à jour du système..."
    apt update && apt upgrade -y
    apt install -y curl wget git unzip software-properties-common
    log_success "Système mis à jour"
}

# Installation PHP
install_php() {
    log_info "Installation de PHP $PHP_VERSION..."
    
    add-apt-repository ppa:ondrej/php -y
    apt update
    
    apt install -y \
        php$PHP_VERSION-fpm \
        php$PHP_VERSION-cli \
        php$PHP_VERSION-mysql \
        php$PHP_VERSION-redis \
        php$PHP_VERSION-curl \
        php$PHP_VERSION-gd \
        php$PHP_VERSION-mbstring \
        php$PHP_VERSION-xml \
        php$PHP_VERSION-zip \
        php$PHP_VERSION-bcmath \
        php$PHP_VERSION-soap \
        php$PHP_VERSION-intl \
        php$PHP_VERSION-readline \
        php$PHP_VERSION-msgpack \
        php$PHP_VERSION-igbinary
    
    log_success "PHP installé"
}

# Configuration PHP pour production
configure_php() {
    log_info "Configuration PHP pour la production..."
    
    PHP_INI="/etc/php/$PHP_VERSION/fpm/php.ini"
    
    # Optimisations sécurité et performance
    sed -i 's/expose_php = On/expose_php = Off/' $PHP_INI
    sed -i 's/;opcache.enable=1/opcache.enable=1/' $PHP_INI
    sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' $PHP_INI
    sed -i 's/;opcache.max_accelerated_files=4000/opcache.max_accelerated_files=10000/' $PHP_INI
    sed -i 's/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/' $PHP_INI
    sed -i 's/memory_limit = 128M/memory_limit = 512M/' $PHP_INI
    sed -i 's/max_execution_time = 30/max_execution_time = 300/' $PHP_INI
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' $PHP_INI
    sed -i 's/post_max_size = 8M/post_max_size = 12M/' $PHP_INI
    
    systemctl restart php$PHP_VERSION-fpm
    log_success "PHP configuré"
}

# Installation MySQL
install_mysql() {
    log_info "Installation de MySQL..."
    
    if [[ -z "$MYSQL_ROOT_PASSWORD" ]]; then
        read -s -p "Mot de passe root MySQL: " MYSQL_ROOT_PASSWORD
        echo
    fi
    
    debconf-set-selections <<< "mysql-server mysql-server/root_password password $MYSQL_ROOT_PASSWORD"
    debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD"
    
    apt install -y mysql-server
    
    # Sécurisation MySQL
    mysql -u root -p$MYSQL_ROOT_PASSWORD <<EOF
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
EOF
    
    log_success "MySQL installé et sécurisé"
}

# Installation Redis
install_redis() {
    log_info "Installation de Redis..."
    
    apt install -y redis-server
    
    # Configuration Redis pour production
    sed -i 's/# maxmemory <bytes>/maxmemory 256mb/' /etc/redis/redis.conf
    sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
    
    systemctl restart redis-server
    systemctl enable redis-server
    
    log_success "Redis installé"
}

# Installation Nginx
install_nginx() {
    log_info "Installation de Nginx..."
    
    apt install -y nginx
    
    # Configuration Nginx pour Astrolab
    cat > /etc/nginx/sites-available/astrolab <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;
    index index.php index.html;

    # Sécurité
    server_tokens off;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gestion des fichiers statiques
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Laravel
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        
        # Sécurité PHP
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    # Interdire l'accès aux fichiers sensibles
    location ~ /\. {
        deny all;
    }
    
    location ~ /(\.env|\.git|composer\.|package\.|artisan) {
        deny all;
    }
}
EOF

    ln -sf /etc/nginx/sites-available/astrolab /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    nginx -t && systemctl restart nginx
    log_success "Nginx installé et configuré"
}

# Installation Composer
install_composer() {
    log_info "Installation de Composer..."
    
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    
    log_success "Composer installé"
}

# Installation Node.js
install_nodejs() {
    log_info "Installation de Node.js..."
    
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
    
    log_success "Node.js installé"
}

# Création utilisateur application
create_app_user() {
    log_info "Création de l'utilisateur $APP_USER..."
    
    useradd -m -s /bin/bash $APP_USER
    usermod -aG www-data $APP_USER
    
    log_success "Utilisateur créé"
}

# Configuration base de données
setup_database() {
    log_info "Configuration de la base de données..."
    
    if [[ -z "$APP_DB_PASSWORD" ]]; then
        read -s -p "Mot de passe base de données Astrolab: " APP_DB_PASSWORD
        echo
    fi
    
    mysql -u root -p$MYSQL_ROOT_PASSWORD <<EOF
CREATE DATABASE astrolab_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'astrolab_user'@'localhost' IDENTIFIED BY '$APP_DB_PASSWORD';
GRANT ALL PRIVILEGES ON astrolab_production.* TO 'astrolab_user'@'localhost';
FLUSH PRIVILEGES;
EOF
    
    log_success "Base de données configurée"
}

# Configuration SSL avec Let's Encrypt
setup_ssl() {
    log_info "Configuration SSL avec Let's Encrypt..."
    
    apt install -y certbot python3-certbot-nginx
    
    # Obtenir le certificat
    certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
    
    # Auto-renouvellement
    echo "0 12 * * * /usr/bin/certbot renew --quiet" | crontab -
    
    log_success "SSL configuré"
}

# Configuration firewall
setup_firewall() {
    log_info "Configuration du firewall..."
    
    ufw --force enable
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow 'Nginx Full'
    
    log_success "Firewall configuré"
}

# Configuration monitoring
setup_monitoring() {
    log_info "Configuration du monitoring..."
    
    # Logrotate pour les logs Laravel
    cat > /etc/logrotate.d/astrolab <<EOF
$APP_DIR/storage/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    missingok
    notifempty
    create 644 www-data www-data
}
EOF

    # Surveillance des processus
    apt install -y supervisor
    
    cat > /etc/supervisor/conf.d/astrolab-worker.conf <<EOF
[program:astrolab-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

    systemctl restart supervisor
    
    log_success "Monitoring configuré"
}

# Fonction principale
main() {
    if [[ $EUID -ne 0 ]]; then
        log_error "Ce script doit être exécuté en tant que root"
        exit 1
    fi
    
    log_info "=== DÉBUT DE L'INSTALLATION ==="
    
    read -p "Nom de domaine (ex: astrolab.com): " DOMAIN
    
    update_system
    install_php
    configure_php
    install_mysql
    install_redis
    install_nginx
    install_composer
    install_nodejs
    create_app_user
    setup_database
    setup_firewall
    setup_monitoring
    
    log_warning "Configuration SSL manuelle requise:"
    log_info "sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
    
    log_success "=== INSTALLATION SERVEUR TERMINÉE ==="
    log_info "Prochaines étapes:"
    log_info "1. Déployez votre code dans $APP_DIR"
    log_info "2. Configurez le fichier .env"
    log_info "3. Exécutez le script de déploiement"
}

# Exécution
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
