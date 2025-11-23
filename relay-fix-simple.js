
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Script points relais chargé');
    
    const relayList = document.getElementById('relay-list');
    const selectedRelayPoint = document.getElementById('selected-relay-point');
    
    // Fonction simple de sélection
    window.selectRelayPoint = function(point) {
        console.log('📍 Sélection point relais:', point);
        
        if (!selectedRelayPoint) {
            alert('Erreur: champ sélection manquant');
            return;
        }
        
        try {
            // Stocker le point
            selectedRelayPoint.value = JSON.stringify(point);
            
            // Effet visuel
            document.querySelectorAll('.relay-point').forEach(rp => {
                rp.style.background = 'rgba(255,255,255,0.15)';
                rp.style.borderColor = 'rgba(255,255,255,0.2)';
            });
            
            const selectedDiv = document.querySelector(`[data-point-id="${point.id}"]`);
            if (selectedDiv) {
                selectedDiv.style.background = 'rgba(39,174,96,0.25)';
                selectedDiv.style.borderColor = 'rgba(39,174,96,0.4)';
            }
            
            // Notification simple
            const notification = document.createElement('div');
            notification.innerHTML = '✅ Point sélectionné: ' + point.name;
            notification.style.cssText = `
                position: fixed; top: 20px; right: 20px; z-index: 9999;
                background: #27ae60; color: white; padding: 15px 20px;
                border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 3000);
            
            console.log('✅ Point sélectionné avec succès');
            
        } catch (error) {
            console.error('❌ Erreur sélection:', error);
            alert('Erreur lors de la sélection');
        }
    };
    
    // Attacher les événements après chargement des points
    function attachRelayEvents() {
        document.querySelectorAll('.select-relay-btn').forEach((btn, index) => {
            console.log('🔗 Attachement événement bouton', index);
            
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🖱️ Clic bouton détecté');
                
                try {
                    const pointData = this.getAttribute('data-point');
                    console.log('📊 Data brute:', pointData);
                    
                    if (!pointData) {
                        alert('Erreur: données point manquantes');
                        return;
                    }
                    
                    const point = JSON.parse(pointData.replace(/&apos;/g, "'"));
                    console.log('📦 Point parsé:', point);
                    
                    selectRelayPoint(point);
                    
                } catch (error) {
                    console.error('❌ Erreur parse:', error);
                    alert('Erreur données point: ' + error.message);
                }
            };
        });
        
        console.log('✅ Événements attachés à', document.querySelectorAll('.select-relay-btn').length, 'boutons');
    }
    
    // Observer pour détecter l'ajout de nouveaux boutons
    if (relayList) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    console.log('🔄 Nouveaux éléments détectés, réattachement événements');
                    setTimeout(attachRelayEvents, 100);
                }
            });
        });
        
        observer.observe(relayList, { childList: true, subtree: true });
        console.log('👀 Observer configuré pour relayList');
    }
    
    // Test initial
    attachRelayEvents();
});
</script>