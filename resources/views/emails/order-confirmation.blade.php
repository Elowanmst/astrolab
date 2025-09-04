<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - Astrolab</title>
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
            background-color: #000000;
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
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #000000;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .order-info h2 {
            color: #000000;
            font-size: 18px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .info-value {
            color: #000;
            font-weight: bold;
        }
        
        .items-section {
            margin-bottom: 30px;
        }
        
        .items-section h2 {
            color: #000000;
            font-size: 18px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .item-variants {
            font-size: 12px;
            color: #666;
        }
        
        .item-quantity {
            text-align: center;
            margin: 0 20px;
            font-weight: bold;
        }
        
        .item-price {
            text-align: right;
            font-weight: bold;
            color: #000;
        }
        
        .shipping-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .shipping-section h2 {
            color: #000000;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .total-section {
            background-color: #000000;
            color: #ffffff;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .payment-info {
            margin-top: 15px;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .next-steps {
            margin-top: 30px;
        }
        
        .next-steps h2 {
            color: #000000;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .step {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .step-icon {
            background-color: #000000;
            color: #ffffff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .step-content h3 {
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }
        
        .step-content p {
            color: #666;
            font-size: 14px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .contact-info {
            color: #000;
            font-weight: bold;
        }
        
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item-quantity,
            .item-price {
                margin: 10px 0;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>| ASTROLAB |</h1>
            <p>Confirmation de votre commande</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p style="margin-bottom: 20px;">Bonjour {{ $order->shipping_name }},</p>
            <p style="margin-bottom: 30px;">
                Merci pour votre commande ! Nous avons bien reçu votre 
                @if(config('payment.default_processor') === 'simulation')
                    commande de test
                @else
                    paiement
                @endif
                et nous préparons déjà votre colis avec soin.
            </p>
            
            <!-- Order Information -->
            <div class="order-info">
                <h2>Informations de commande</h2>
                <div class="info-row">
                    <span class="info-label">Numéro de commande</span>
                    <span class="info-value">#{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date de commande</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut</span>
                    <span class="info-value">{{ $order->getStatusLabel() }}</span>
                </div>
                @if($order->payment_status && $order->payment_status !== 'pending')
                <div class="info-row">
                    <span class="info-label">Paiement</span>
                    <span class="info-value">
                        @if(config('payment.default_processor') === 'simulation')
                            Simulé avec succès
                        @else
                            {{ ucfirst($order->payment_status) }}
                        @endif
                    </span>
                </div>
                @endif
            </div>
            
            <!-- Order Items -->
            <div class="items-section">
                <h2>Articles commandés</h2>
                @foreach($order->items as $item)
                <div class="item">
                    <div class="item-details">
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->size || $item->color)
                        <div class="item-variants">
                            @if($item->size)Taille: {{ $item->size }}@endif
                            @if($item->size && $item->color) • @endif
                            @if($item->color)Couleur: {{ $item->color }}@endif
                        </div>
                        @endif
                    </div>
                    <div class="item-quantity">
                        Qté: {{ $item->quantity }}
                    </div>
                    <div class="item-price">
                        {{ number_format($item->product_price * $item->quantity, 2) }} €
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Shipping Information -->
            <div class="shipping-section">
                <h2>Adresse de livraison</h2>
                <p><strong>{{ $order->shipping_name }}</strong></p>
                <p>{{ $order->shipping_address }}</p>
                <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                <p style="margin-top: 10px; color: #666;">{{ $order->shipping_email }}</p>
            </div>
            
            <!-- Total -->
            <div class="total-section">
                <div class="total-amount">{{ number_format($order->total_amount, 2) }} €</div>
                <div class="payment-info">
                    TTC • Paiement sécurisé
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="next-steps">
                <h2>Prochaines étapes</h2>
                
                <div class="step">
                    <div class="step-icon">1</div>
                    <div class="step-content">
                        <h3>Préparation</h3>
                        <p>Votre commande est en cours de préparation dans nos ateliers (24-48h ouvrées)</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-icon">2</div>
                    <div class="step-content">
                        <h3>Expédition</h3>
                        <p>Vous recevrez un email avec le numéro de suivi dès l'expédition</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-icon">3</div>
                    <div class="step-content">
                        <h3>Livraison</h3>
                        <p>Livraison estimée dans 2-4 jours ouvrés après expédition</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p>Pour toute question : <span class="contact-info">contact@astrolab.com</span></p>
            <p style="margin-top: 15px; font-style: italic;">
                Merci de faire confiance à Astrolab pour vos éditions éphémères exclusives.
            </p>
        </div>
    </div>
</body>
</html>
