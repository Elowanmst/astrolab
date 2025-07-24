<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande - Astrolab Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .header .notification-badge {
            background-color: #ff4444;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-weight: bold;
        }
        
        .order-summary {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .order-summary h2 {
            color: #007bff;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .value {
            color: #000;
            font-weight: bold;
        }
        
        .customer-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        
        .customer-info h2 {
            color: #000;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #adb5bd;
            padding-bottom: 8px;
        }
        
        .items-section {
            margin-bottom: 25px;
        }
        
        .items-section h2 {
            color: #000;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: bold;
            color: #000;
        }
        
        .item-details {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        
        .item-qty {
            background-color: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .item-price {
            font-weight: bold;
            color: #000;
        }
        
        .actions {
            background-color: #000;
            color: white;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }
        
        .actions h2 {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .action-button {
            background-color: #ffffff;
            color: #000000;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .total-highlight {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .payment-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .payment-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .payment-simulation {
            background-color: #cce7ff;
            color: #004085;
        }
        
        @media (max-width: 600px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>| ASTROLAB ADMIN |</h1>
            <div class="notification-badge">🔔 NOUVELLE COMMANDE</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Alert -->
            <div class="alert">
                ⚡ Action requise : Une nouvelle commande vient d'être passée et nécessite votre attention !
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h2>📋 Résumé de la commande</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="label">Numéro</span>
                        <span class="value">#{{ $order->order_number }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Date</span>
                        <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Statut</span>
                        <span class="value">{{ $order->getStatusLabel() }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Paiement</span>
                        <span class="value">
                            @if(config('payment.default_processor') === 'simulation')
                                <span class="payment-status payment-simulation">Mode Test</span>
                            @elseif($order->payment_status === 'paid')
                                <span class="payment-status payment-paid">Payé</span>
                            @else
                                <span class="payment-status payment-pending">En attente</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="customer-info">
                <h2>👤 Informations client</h2>
                <p><strong>Nom :</strong> {{ $order->shipping_name }}</p>
                <p><strong>Email :</strong> {{ $order->shipping_email }}</p>
                <p><strong>Adresse :</strong> {{ $order->shipping_address }}</p>
                <p><strong>Ville :</strong> {{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
            </div>
            
            <!-- Items -->
            <div class="items-section">
                <h2>🛍️ Articles commandés ({{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }})</h2>
                @foreach($order->items as $item)
                <div class="item">
                    <div>
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->size || $item->color)
                        <div class="item-details">
                            @if($item->size)Taille: {{ $item->size }}@endif
                            @if($item->size && $item->color) • @endif
                            @if($item->color)Couleur: {{ $item->color }}@endif
                        </div>
                        @endif
                    </div>
                    <div class="item-qty">{{ $item->quantity }}</div>
                    <div class="item-price">{{ number_format($item->product_price * $item->quantity, 2) }} €</div>
                </div>
                @endforeach
            </div>
            
            <!-- Total -->
            <div class="total-highlight">
                💰 TOTAL : {{ number_format($order->total_amount, 2) }} €
                @if(config('payment.default_processor') === 'simulation')
                    (Mode Test)
                @endif
            </div>
            
            <!-- Actions -->
            <div class="actions">
                <h2>⚡ Actions rapides</h2>
                <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}" class="action-button">
                    📊 Voir dans Filament
                </a>
                <a href="mailto:{{ $order->shipping_email }}" class="action-button">
                    ✉️ Contacter client
                </a>
            </div>
            
            <!-- Footer Info -->
            <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 6px; font-size: 12px; color: #666;">
                <p><strong>Rappel :</strong></p>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <li>Vérifier le stock des produits commandés</li>
                    <li>Préparer la commande dans les 24-48h</li>
                    <li>Mettre à jour le statut dans l'admin</li>
                    <li>Générer l'étiquette d'expédition</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
