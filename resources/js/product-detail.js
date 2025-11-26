// === JAVASCRIPT POUR PAGE DÉTAIL PRODUIT ===

document.addEventListener('DOMContentLoaded', function() {
    // === GALERIE D'IMAGES ===
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    const mainImages = document.querySelectorAll('.main-product-image');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const imageIndex = this.dataset.imageIndex;
            
            // Retirer la classe active de toutes les miniatures et images principales
            thumbnails.forEach(t => t.classList.remove('active'));
            mainImages.forEach(img => img.classList.remove('active'));
            
            // Ajouter la classe active à la miniature cliquée et l'image correspondante
            this.classList.add('active');
            const targetImage = document.querySelector(`[data-image-index="${imageIndex}"].main-product-image`);
            if (targetImage) {
                targetImage.classList.add('active');
            }
        });
    });

    // === SÉLECTION DES TAILLES AVEC GESTION DU STOCK ===
    const sizeOptions = document.querySelectorAll('.size-option');
    const selectedSizeInput = document.getElementById('selected-size');
    const quantityInput = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const quantityStockInfo = document.getElementById('quantity-stock-info');

    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Ignorer si la taille est désactivée
            if (this.disabled || this.classList.contains('size-disabled')) {
                return;
            }

            // Retirer la sélection précédente
            sizeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option cliquée
            this.classList.add('selected');
            
            // Mettre à jour le champ caché
            const size = this.dataset.size;
            const stock = parseInt(this.dataset.stock) || 0;
            selectedSizeInput.value = size;
            
            // Mettre à jour la quantité maximum
            quantityInput.max = stock;
            if (parseInt(quantityInput.value) > stock) {
                quantityInput.value = Math.min(stock, 1);
            }
            
            // Mettre à jour le bouton d'ajout au panier
            if (stock > 0) {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Ajouter au panier';
            } else {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-ban"></i> Rupture de stock';
            }
            
            // Mettre à jour l'info de stock
            updateStockInfo(stock);
        });
    });

    // Fonction pour mettre à jour l'affichage du stock
    function updateStockInfo(stock) {
        let message = '';
        let className = '';
        
        if (stock === 0) {
            message = 'Rupture de stock';
            className = 'stock-out';
        } else if (stock <= 5) {
            message = `Stock faible : ${stock} restant${stock > 1 ? 's' : ''}`;
            className = 'stock-low';
        } else if (stock <= 50) {
            message = `${stock} en stock`;
            className = 'stock-good';
        } else {
            // Stock > 50 : ne pas afficher l'information de stock
            message = '';
            className = '';
        }
        
        quantityStockInfo.textContent = message;
        quantityStockInfo.className = `stock-info ${className}`;
        
        // Masquer l'élément si pas de message
        if (message === '') {
            quantityStockInfo.style.display = 'none';
        } else {
            quantityStockInfo.style.display = 'block';
        }
    }

    // === SÉLECTION DES COULEURS ===
    const colorInputs = document.querySelectorAll('.color-input');
    
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optionnel : logique supplémentaire lors du changement de couleur
            console.log('Couleur sélectionnée:', this.value);
        });
    });

    // === VALIDATION DU FORMULAIRE ===
    const addToCartForm = document.querySelector('.add-to-cart-form');
    
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            // Vérifier si une taille est sélectionnée (optionnel pour l'instant)
            if (!selectedSizeInput.value) {
                // Sélectionner automatiquement la taille M par défaut
                const defaultSize = document.querySelector('[data-size="M"]');
                if (defaultSize) {
                    defaultSize.click();
                }
            }

            // Vérifier si une couleur est sélectionnée (déjà gérée par le checked par défaut)
            const selectedColor = document.querySelector('.color-input:checked');
            if (!selectedColor && document.querySelectorAll('.color-input').length > 0) {
                // Sélectionner la première couleur par défaut
                const firstColor = document.querySelector('.color-input');
                if (firstColor) {
                    firstColor.checked = true;
                }
            }

            // Afficher un message de confirmation
            setTimeout(() => {
                showNotification('Produit ajouté au panier !', 'success');
            }, 100);
        });
    }
});

// === FONCTIONS UTILITAIRES ===

// Changer la quantité avec vérification du stock
function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const selectedSize = document.getElementById('selected-size').value;
    
    if (!selectedSize) {
        showNotification('Veuillez d\'abord sélectionner une taille', 'error');
        return;
    }
    
    const currentValue = parseInt(quantityInput.value);
    const minValue = parseInt(quantityInput.min) || 1;
    const maxValue = parseInt(quantityInput.max) || 1;
    
    const newValue = currentValue + change;
    
    if (newValue >= minValue && newValue <= maxValue) {
        quantityInput.value = newValue;
    } else if (newValue > maxValue) {
        showNotification(`Stock maximum disponible: ${maxValue}`, 'warning');
    }
}

// Ouvrir le guide des tailles
function openSizeGuide() {
    const modal = document.getElementById('sizeGuideModal');
    if (modal) {
        modal.classList.add('active');
        modal.style.display = 'flex';
        
        // Fermer avec Escape
        document.addEventListener('keydown', closeSizeGuideOnEscape);
        
        // Fermer en cliquant à l'extérieur
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeSizeGuide();
            }
        });
    }
}

// Fermer le guide des tailles
function closeSizeGuide() {
    const modal = document.getElementById('sizeGuideModal');
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none';
        document.removeEventListener('keydown', closeSizeGuideOnEscape);
    }
}

// Fermer le guide avec Escape
function closeSizeGuideOnEscape(e) {
    if (e.key === 'Escape') {
        closeSizeGuide();
    }
}

// Ajouter aux favoris
function addToWishlist(productId) {
    // Ici vous pouvez ajouter la logique pour sauvegarder en favoris
    // Pour l'instant, juste une notification
    showNotification('Produit ajouté aux favoris !', 'success');
    
    // Changer l'icône du bouton
    const wishlistBtn = document.querySelector('.btn-wishlist i');
    if (wishlistBtn) {
        wishlistBtn.classList.remove('far');
        wishlistBtn.classList.add('fas');
    }
}

// Partager le produit
function shareProduct() {
    if (navigator.share) {
        // API Web Share si disponible
        navigator.share({
            title: document.title,
            url: window.location.href,
        }).catch(err => console.log('Erreur lors du partage:', err));
    } else {
        // Fallback : copier le lien
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Lien copié dans le presse-papiers !', 'success');
        }).catch(() => {
            showNotification('Impossible de copier le lien', 'error');
        });
    }
}

// Afficher une notification
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Styles pour la notification
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '8px',
        color: 'white',
        fontWeight: 'bold',
        zIndex: '10000',
        opacity: '0',
        transform: 'translateY(-20px)',
        transition: 'all 0.3s ease',
        maxWidth: '300px',
        wordWrap: 'break-word'
    });
    
    // Couleurs selon le type
    switch(type) {
        case 'success':
            notification.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            break;
        case 'error':
            notification.style.background = 'linear-gradient(135deg, #dc3545, #e74c3c)';
            break;
        default:
            notification.style.background = 'linear-gradient(135deg, #007bff, #0056b3)';
    }
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Animation d'apparition
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Suppression automatique
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
    
    // Suppression au clic
    notification.addEventListener('click', () => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    });
}

// === AMÉLIORATION DE L'ACCESSIBILITÉ ===

// Navigation au clavier dans la galerie
document.addEventListener('keydown', function(e) {
    if (e.target.classList.contains('thumbnail-item')) {
        const thumbnails = Array.from(document.querySelectorAll('.thumbnail-item'));
        const currentIndex = thumbnails.indexOf(e.target);
        
        let targetIndex;
        
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                targetIndex = currentIndex > 0 ? currentIndex - 1 : thumbnails.length - 1;
                break;
            case 'ArrowRight':
                e.preventDefault();
                targetIndex = currentIndex < thumbnails.length - 1 ? currentIndex + 1 : 0;
                break;
            case 'Enter':
            case ' ':
                e.preventDefault();
                e.target.click();
                return;
            default:
                return;
        }
        
        if (targetIndex !== undefined) {
            thumbnails[targetIndex].focus();
        }
    }
});

// === OPTIMISATIONS PERFORMANCE ===

// Lazy loading pour les images
const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        }
    });
});

// Observer toutes les images avec data-src
document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
});

// === GESTION DES ERREURS D'IMAGES ===
document.querySelectorAll('.main-product-image, .thumbnail-item img').forEach(img => {
    img.addEventListener('error', function() {
        this.style.display = 'none';
        
        // Afficher un placeholder si c'est l'image principale
        if (this.classList.contains('main-product-image')) {
            const placeholder = this.parentNode.querySelector('.no-image-placeholder');
            if (!placeholder) {
                const newPlaceholder = document.createElement('div');
                newPlaceholder.className = 'no-image-placeholder';
                newPlaceholder.innerHTML = '<i class="fas fa-image"></i><p>Image non disponible</p>';
                this.parentNode.appendChild(newPlaceholder);
            }
        }
    });
});

// === SAUVEGARDE LOCAL DES PRÉFÉRENCES ===
function saveUserPreferences() {
    const selectedSize = document.getElementById('selected-size').value;
    const selectedColor = document.querySelector('.color-input:checked')?.value;
    
    if (selectedSize || selectedColor) {
        const preferences = {
            size: selectedSize,
            color: selectedColor,
            timestamp: Date.now()
        };
        
        localStorage.setItem('product-preferences', JSON.stringify(preferences));
    }
}

// Restaurer les préférences sauvegardées
function restoreUserPreferences() {
    const saved = localStorage.getItem('product-preferences');
    if (saved) {
        try {
            const preferences = JSON.parse(saved);
            
            // Ne restaurer que si c'est récent (moins d'1 heure)
            if (Date.now() - preferences.timestamp < 3600000) {
                if (preferences.size) {
                    const sizeButton = document.querySelector(`[data-size="${preferences.size}"]`);
                    if (sizeButton) {
                        sizeButton.click();
                    }
                }
                
                if (preferences.color) {
                    const colorInput = document.querySelector(`[value="${preferences.color}"]`);
                    if (colorInput) {
                        colorInput.checked = true;
                    }
                }
            }
        } catch (e) {
            console.warn('Erreur lors de la restauration des préférences:', e);
        }
    }
}

// Initialiser la restauration des préférences
setTimeout(restoreUserPreferences, 500);

// Sauvegarder les préférences lors des changements
document.addEventListener('change', saveUserPreferences);
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('size-option')) {
        setTimeout(saveUserPreferences, 100);
    }
});
