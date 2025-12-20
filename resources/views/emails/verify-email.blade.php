<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de votre email - Astrolab</title>
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
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 18px;
            margin-bottom: 20px;
            color: #000;
            font-weight: bold;
        }
        
        .message {
            margin-bottom: 30px;
            color: #666;
        }
        
        .action-button {
            background-color: #000000;
            color: #ffffff;
            padding: 15px 30px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.3s;
        }
        
        .action-button:hover {
            background-color: #333333;
        }
        
        .expiry-note {
            font-size: 12px;
            color: #999;
            margin-top: 30px;
            font-style: italic;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        
        .link-break {
            word-break: break-all;
            color: #ffffffff;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>| ASTROLAB |</h1>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="welcome-text">Bienvenue chez Astrolab ! 🚀</div>
            
            <p class="message">
                Merci de vous être inscrit(e). Pour activer votre compte et commencer vos achats, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous.
            </p>
            
            <a href="{{ $url }}" class="action-button">
                Vérifier mon email
            </a>
            
            <p class="expiry-note">
                Ce lien est valide pendant 60 minutes. Si vous n'avez pas créé de compte chez Astrolab, vous pouvez ignorer cet email.
            </p>
            
            <div style="margin-top: 40px; text-align: left; font-size: 12px; color: #999;">
                <p>Si vous rencontrez des problèmes avec le bouton, copiez et collez l'URL ci-dessous dans votre navigateur web :</p>
                <p class="link-break">{{ $url }}</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Astrolab. Tous droits réservés.
        </div>
    </div>
</body>
</html>
