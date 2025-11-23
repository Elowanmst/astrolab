#!/usr/bin/env php
<?php

echo "🔍 === DIAGNOSTIC COMPLET POINTS RELAIS ===\n";
echo "📅 Date: " . date('d/m/Y H:i:s') . "\n\n";

$file = '/Users/elowanmestres/Documents/GitHub/astrolab/resources/views/checkout/shipping.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier shipping.blade.php introuvable\n";
    exit(1);
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

echo "📊 Fichier: " . basename($file) . " (" . count($lines) . " lignes)\n\n";

// Rechercher les problèmes potentiels
echo "🔍 === RECHERCHE DE PROBLÈMES ===\n";

// 1. Vérifier les fonctions critiques
$functionsToCheck = [
    'function selectRelayPoint(' => 'Fonction selectRelayPoint',
    'function displayRelayPoints(' => 'Fonction displayRelayPoints',
    'function generatePointHTML(' => 'Fonction generatePointHTML',
    '.select-relay-btn' => 'Sélecteurs boutons',
    'addEventListener(\'click\'' => 'Event listeners',
];

foreach ($functionsToCheck as $search => $name) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        echo "✅ $name: trouvé ($count occurrences)\n";
    } else {
        echo "❌ $name: MANQUANT\n";
    }
}

echo "\n🔧 === ANALYSE STRUCTURE JAVASCRIPT ===\n";

// Rechercher les blocs JS problématiques
$jsProblems = [];

// Vérifier les accolades
$openBraces = substr_count($content, '{');
$closeBraces = substr_count($content, '}');
if ($openBraces !== $closeBraces) {
    $jsProblems[] = "Accolades non équilibrées: {$openBraces} ouvertes vs {$closeBraces} fermées";
}

// Vérifier les parenthèses
$openParens = substr_count($content, '(');
$closeParens = substr_count($content, ')');
if ($openParens !== $closeParens) {
    $jsProblems[] = "Parenthèses non équilibrées: {$openParens} ouvertes vs {$closeParens} fermées";
}

// Vérifier les balises script
$scriptTags = substr_count($content, '<script>');
$scriptCloseTags = substr_count($content, '</script>');
if ($scriptTags !== $scriptCloseTags) {
    $jsProblems[] = "Balises script non équilibrées: {$scriptTags} ouvertures vs {$scriptCloseTags} fermetures";
}

if (empty($jsProblems)) {
    echo "✅ Structure JavaScript semble correcte\n";
} else {
    foreach ($jsProblems as $problem) {
        echo "❌ $problem\n";
    }
}

echo "\n📋 === RECHERCHE LIGNES PROBLÉMATIQUES ===\n";

$problematicPatterns = [
    '/function\s+\w+\([^)]*\)\s*{\s*function/' => 'Fonctions imbriquées incorrectes',
    '/}\s*function/' => 'Fonctions mal fermées',
    '/addEventListener.*{[^}]*$/' => 'Event listeners non fermés',
    '/JSON\.stringify\([^)]+\)(?!\s*\.replace)/' => 'JSON.stringify sans échappement'
];

foreach ($problematicPatterns as $pattern => $description) {
    if (preg_match($pattern, $content)) {
        echo "⚠️ Détecté: $description\n";
    }
}

echo "\n🎯 === SOLUTION RECOMMANDÉE ===\n";

// Créer une version corrigée simplifiée
$fixedJS = "
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
            
            const selectedDiv = document.querySelector(`[data-point-id=\"\${point.id}\"]`);
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
                    
                    const point = JSON.parse(pointData.replace(/&apos;/g, \"'\"));
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
</script>";

echo "Je vais maintenant appliquer une solution simplifiée et robuste...\n";

file_put_contents('/Users/elowanmestres/Documents/GitHub/astrolab/relay-fix-simple.js', $fixedJS);

echo "\n✅ Script de correction créé: relay-fix-simple.js\n";
echo "\n🔧 PROCHAINES ÉTAPES:\n";
echo "1. Je vais remplacer le JavaScript problématique\n";
echo "2. Utiliser une approche plus simple et robuste\n";
echo "3. Ajouter des logs de debug complets\n";
echo "4. Tester avec votre navigateur\n";
