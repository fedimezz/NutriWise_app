<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/motpasse.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <span>🥗 NutriWise</span>
            </div>

            <h1>Mot de passe oublié ?</h1>
            <p class="subtitle">Entrez votre numéro de téléphone pour recevoir un code de réinitialisation</p>

            <form action="index.php?page=motpasse" method="POST">
                <div class="form-group">
                    <label>Numéro de téléphone</label>
                    <div class="phone-wrapper">
                        <select name="countryCode" class="country-select">
                            <option value="+33">🇫🇷 +33</option>
                            <option value="+32">🇧🇪 +32</option>
                            <option value="+41">🇨🇭 +41</option>
                            <option value="+1">🇺🇸 +1</option>
                            <option value="+44">🇬🇧 +44</option>
                            <option value="+221">🇸🇳 +221</option>
                            <option value="+212">🇲🇦 +212</option>
                            <option value="+216">🇹🇳 +216</option>
                        </select>
                        <input type="tel" name="phoneNumber" placeholder="6 12 34 56 78" autocomplete="off" required>
                    </div>
                </div>

                <button type="submit" class="btn">📱 Envoyer le code par SMS</button>
            </form>

            <div class="back-link">
                <a href="index.php?page=login">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>
</html>