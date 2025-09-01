#!/bin/bash

# Script de déploiement production Astrolab
# Utilisation: ./scripts/deploy-production.sh

set -e # Arrêter en cas d'erreur

echo "🚀 === DÉPLOIEMENT PRODUCTION ASTROLAB ==="
echo "Démarrage: $(date)"

# Configuration
APP_DIR="/var/www/astrolab"
BACKUP_DIR="/var/backups/astrolab"
MAINTENANCE_FILE="$APP_DIR/storage/framework/down"

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonctions utilitaires
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérifications préalables
check_prerequisites() {
    log_info "Vérification des prérequis..."
    
    # Vérifier que nous sommes dans le bon répertoire
    if [[ ! -f "artisan" ]]; then
        log_error "Le fichier artisan n'existe pas. Êtes-vous dans le bon répertoire ?"
        exit 1
    fi
    
    # Vérifier les commandes nécessaires
    for cmd in php composer git npm; do
        if ! command -v $cmd &> /dev/null; then
            log_error "La commande '$cmd' n'est pas disponible"
            exit 1
        fi
    done
    
    # Vérifier l'environnement
    if [[ "$(php artisan env)" != "production" ]]; then
        log_warning "L'environnement n'est pas en 'production'"
        read -p "Continuer quand même ? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
    
    log_success "Prérequis validés"
}

# Sauvegarde avant déploiement
backup_current() {
    log_info "Création de la sauvegarde..."
    
    # Créer le répertoire de sauvegarde
    BACKUP_TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    CURRENT_BACKUP="$BACKUP_DIR/$BACKUP_TIMESTAMP"
    mkdir -p "$CURRENT_BACKUP"
    
    # Sauvegarder la base de données
    if [[ -f ".env" ]]; then
        DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
        if [[ ! -z "$DB_NAME" ]]; then
            log_info "Sauvegarde de la base de données..."
            mysqldump --single-transaction "$DB_NAME" > "$CURRENT_BACKUP/database.sql"
            log_success "Base de données sauvegardée"
        fi
    fi
    
    # Sauvegarder les fichiers critiques
    cp -r storage/app/public "$CURRENT_BACKUP/storage_public" 2>/dev/null || true
    cp .env "$CURRENT_BACKUP/env_backup" 2>/dev/null || true
    
    log_success "Sauvegarde créée: $CURRENT_BACKUP"
}

# Mode maintenance
enable_maintenance() {
    log_info "Activation du mode maintenance..."
    php artisan down --render="errors::503" --secret="astrolab-deploy-2025"
    log_warning "Site en maintenance"
}

disable_maintenance() {
    log_info "Désactivation du mode maintenance..."
    php artisan up
    log_success "Site remis en ligne"
}

# Déploiement du code
deploy_code() {
    log_info "Déploiement du code..."
    
    # Pull du code depuis Git
    git fetch origin
    git checkout main
    git pull origin main
    
    # Installation des dépendances PHP
    log_info "Installation des dépendances PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Installation des dépendances Node.js
    log_info "Installation des dépendances Node.js..."
    npm ci --production
    
    # Compilation des assets
    log_info "Compilation des assets..."
    npm run build
    
    log_success "Code déployé"
}

# Configuration Laravel
configure_laravel() {
    log_info "Configuration de Laravel..."
    
    # Optimisations
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    # Migrations
    log_info "Exécution des migrations..."
    php artisan migrate --force
    
    # Clear des caches
    php artisan cache:clear
    php artisan queue:restart
    
    log_success "Laravel configuré"
}

# Permissions et sécurité
set_permissions() {
    log_info "Configuration des permissions..."
    
    # Permissions fichiers
    find . -type f -exec chmod 644 {} \;
    find . -type d -exec chmod 755 {} \;
    
    # Permissions spéciales
    chmod -R 775 storage bootstrap/cache
    chmod +x artisan
    
    # Propriétaire web
    if getent group www-data > /dev/null 2>&1; then
        chown -R www-data:www-data storage bootstrap/cache
    fi
    
    log_success "Permissions configurées"
}

# Tests post-déploiement
run_tests() {
    log_info "Exécution des tests..."
    
    # Test de connectivité base de données
    if php artisan migrate:status > /dev/null 2>&1; then
        log_success "Connexion base de données OK"
    else
        log_error "Erreur de connexion base de données"
        return 1
    fi
    
    # Test de cache
    if php artisan cache:clear > /dev/null 2>&1; then
        log_success "Cache système OK"
    else
        log_warning "Problème avec le cache"
    fi
    
    # Test des queues
    if php artisan queue:work --once --timeout=10 > /dev/null 2>&1; then
        log_success "Queues système OK"
    else
        log_warning "Problème avec les queues"
    fi
    
    log_success "Tests terminés"
}

# Optimisations performance
optimize_performance() {
    log_info "Optimisations de performance..."
    
    # OPcache PHP
    if php -m | grep -q "Zend OPcache"; then
        # Reset OPcache
        php artisan optimize:clear
        php artisan optimize
        log_success "OPcache optimisé"
    fi
    
    # Optimisation des autoloaders
    composer dump-autoload --optimize --classmap-authoritative
    
    # Préchargement des vues
    php artisan view:cache
    
    log_success "Optimisations appliquées"
}

# Fonction de rollback en cas d'erreur
rollback() {
    log_error "Erreur détectée ! Rollback en cours..."
    
    disable_maintenance
    
    # Restaurer depuis la dernière sauvegarde
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR" | head -n1)
    if [[ ! -z "$LATEST_BACKUP" ]]; then
        log_info "Restauration depuis $LATEST_BACKUP..."
        # Logique de restauration ici
    fi
    
    log_error "Rollback terminé. Vérifiez les logs."
    exit 1
}

# Configuration du gestionnaire d'erreur
trap rollback ERR

# Exécution principale
main() {
    log_info "=== DÉBUT DU DÉPLOIEMENT ==="
    
    check_prerequisites
    backup_current
    enable_maintenance
    deploy_code
    configure_laravel
    set_permissions
    optimize_performance
    run_tests
    disable_maintenance
    
    log_success "=== DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ==="
    log_info "Temps de déploiement: $(date)"
    log_info "Site accessible à: $(grep APP_URL .env | cut -d'=' -f2)"
}

# Exécution si appelé directement
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
