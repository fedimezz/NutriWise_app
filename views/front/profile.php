<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <span>🌿</span>
                <span>NutriWise</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php?page=profile" class="nav-item active">👤 Mon profil</a>
                <a href="#" class="nav-item">📊 Mon tableau</a>
                <a href="#" class="nav-item">🥗 Mes repas</a>
                <a href="#" class="nav-item">📖 Mes recettes</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="profile-header">
                <div class="profile-avatar">
                    <div class="avatar-large">👤</div>
                    <h1><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h1>
                </div>
            </header>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-value"><?= $userData['imc'] ? $userData['imc'] : 'N/A' ?></div>
                    <div class="stat-label">IMC</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $userData['poids'] ? $userData['poids'] : 'N/A' ?></div>
                    <div class="stat-label">Poids (kg)</div>
                </div>
            </div>

            <div class="profile-section">
                <h2>Informations personnelles</h2>

                <?php if(isset($success)): ?>
                    <p class="alert-message" style="color:green; font-weight:bold; margin-bottom:15px;"><?= $success ?></p>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <p class="alert-message" style="color:red; margin-bottom:15px;"><?= $error ?></p>
                <?php endif; ?>

                <form class="profile-form" action="index.php?page=profile" method="POST">
                    <div class="form-row">
                        <div class="form-group"><label>Prénom</label><input type="text" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" required></div>
                        <div class="form-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required></div>
                        <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Taille (cm)</label><input type="number" name="taille" value="<?= htmlspecialchars($userData['taille'] ?? '') ?>"></div>
                        <div class="form-group"><label>Poids (kg)</label><input type="number" step="0.1" name="poids" value="<?= htmlspecialchars($userData['poids'] ?? '') ?>"></div>
                    </div>
                    <div class="form-group">
                        <label>Objectif</label>
                        <select name="objectif">
                            <option value="Perte de poids" <?= ($userData['objectif'] == 'Perte de poids') ? 'selected' : '' ?>>Perte de poids</option>
                            <option value="Maintien" <?= ($userData['objectif'] == 'Maintien') ? 'selected' : '' ?>>Maintien</option>
                            <option value="Prise de muscle" <?= ($userData['objectif'] == 'Prise de muscle') ? 'selected' : '' ?>>Prise de muscle</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="assest/js/main.js"></script>
</body>
</html>