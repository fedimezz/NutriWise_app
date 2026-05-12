<?php
// views/front/profile.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mon profil - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .profile-container {
            display: flex;
            max-width: 1200px;
            margin: 2rem auto;
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .profile-sidebar {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            padding: 2rem;
            width: 300px;
            text-align: center;
            color: white;
        }
        .profile-avatar {
            margin-bottom: 2rem;
        }
        .avatar-frame {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            margin: 0 auto 1rem;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-large {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .avatar-fallback {
            font-size: 3.5rem;
        }
        .upload-btn {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .upload-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .profile-stats {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .stat-item {
            margin-bottom: 1rem;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            display: block;
        }
        .profile-form-container {
            flex: 1;
            padding: 2rem;
        }
        .profile-form-container h1 {
            font-size: 1.8rem;
            color: #2e7d32;
            margin-bottom: 1.5rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e2f;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }
        .btn-save {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46,125,50,0.3);
        }
        .btn-change-password {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8faf8;
            text-decoration: none;
            color: #2c3e2f;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-change-password:hover {
            background: #e8f5e9;
            border-color: #2e7d32;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid #28a745;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid #dc3545;
        }
        .macros-card {
            margin: 14px 0;
            padding: 16px;
            border-radius: 16px;
            background: #f8faf8;
            border: 1px solid #e2e8f0;
        }
        .macros-card strong {
            display: block;
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .macros-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .macro-badge {
            background: white;
            padding: 8px 12px;
            border-radius: 40px;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            .profile-sidebar {
                width: 100%;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .profile-form-container h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="profile-container">
            <!-- Sidebar profil -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <?php
                    $hasProfileImage = !empty($userData['profile_image']);
                    $profileImage = $hasProfileImage ? 'views/uploads/' . $userData['profile_image'] : null;
                    ?>
                    <div class="avatar-frame">
                        <?php if($profileImage && file_exists($profileImage)): ?>
                            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Avatar" class="avatar-large"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;>👤</div>';">
                        <?php else: ?>
                            <div class="avatar-fallback">👤</div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="upload-form" id="profileUploadForm" novalidate>
                        <input type="hidden" name="action" value="upload_image">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                        <label class="upload-btn">
                            📷 Changer la photo
                            <input type="file" name="profile_image" id="profileImageInput" accept="image/*" style="display:none">
                        </label>
                    </form>
                </div>
                
                <h2><?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?></h2>
                <p><?= htmlspecialchars($userData['email']) ?></p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?= $userData['imc'] ?? 'N/A' ?></span>
                        <span>IMC</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= $userData['daily_calories_needs'] ?? '2000' ?></span>
                        <span>Calories/jour</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= ucfirst($userData['role'] ?? 'user') ?></span>
                        <span>Rôle</span>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire profil -->
            <div class="profile-form-container">
                <h1><i class="fas fa-user-circle"></i> Informations personnelles</h1>

                <div style="margin: 10px 0 20px;">
                    <a href="index.php?page=change_password" class="btn-change-password">
                        🔒 Changer mon mot de passe
                    </a>
                </div>
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="profile-form" novalidate>
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Prénom</label>
                            <input type="text" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Téléphone</label>
                            <input type="tel" name="telephone" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-birthday-cake"></i> Âge</label>
                            <input type="number" name="age" value="<?= $userData['age'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Genre</label>
                            <select name="gender">
                                <option value="male" <?= ($userData['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Homme</option>
                                <option value="female" <?= ($userData['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Femme</option>
                                <option value="other" <?= ($userData['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-ruler"></i> Taille (cm)</label>
                            <input type="number" name="taille" step="1" value="<?= $userData['taille'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-weight-scale"></i> Poids (kg)</label>
                            <input type="number" name="poids" step="0.1" value="<?= $userData['poids'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-bullseye"></i> Objectif</label>
                            <select name="objectif">
                                <option value="Perte de poids" <?= ($userData['objectif'] ?? '') == 'Perte de poids' ? 'selected' : '' ?>>🎯 Perte de poids</option>
                                <option value="Maintien" <?= ($userData['objectif'] ?? '') == 'Maintien' ? 'selected' : '' ?>>⚖️ Maintien</option>
                                <option value="Prise de muscle" <?= ($userData['objectif'] ?? '') == 'Prise de muscle' ? 'selected' : '' ?>>💪 Prise de muscle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-running"></i> Niveau d'activité</label>
                            <select name="activity_level">
                                <option value="sedentary" <?= ($userData['activity_level'] ?? '') == 'sedentary' ? 'selected' : '' ?>>🛋️ Sédentaire</option>
                                <option value="light" <?= ($userData['activity_level'] ?? '') == 'light' ? 'selected' : '' ?>>🚶 Léger</option>
                                <option value="moderate" <?= ($userData['activity_level'] ?? '') == 'moderate' ? 'selected' : '' ?>>🏃 Modéré</option>
                                <option value="active" <?= ($userData['activity_level'] ?? '') == 'active' ? 'selected' : '' ?>>🏋️ Actif</option>
                                <option value="very_active" <?= ($userData['activity_level'] ?? '') == 'very_active' ? 'selected' : '' ?>>⚡ Très actif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-allergies"></i> Allergies / Intolérances</label>
                        <input type="text" name="allergies" value="<?= htmlspecialchars($userData['allergies'] ?? '') ?>" placeholder="Ex: arachides, gluten, lactose...">
                        <small>Ajoutez des mots-clés séparés par des virgules</small>
                    </div>

                    <?php
                        $cals = (int)($userData['daily_calories_needs'] ?? 2000);
                        $goal = (string)($userData['objectif'] ?? 'Maintien');
                        if ($goal === 'Perte de poids') { $pPct = 0.30; $cPct = 0.40; $fPct = 0.30; }
                        elseif ($goal === 'Prise de muscle') { $pPct = 0.30; $cPct = 0.50; $fPct = 0.20; }
                        else { $pPct = 0.25; $cPct = 0.50; $fPct = 0.25; }
                        $pG = (int)round(($cals * $pPct) / 4);
                        $cG = (int)round(($cals * $cPct) / 4);
                        $fG = (int)round(($cals * $fPct) / 9);
                    ?>
                    <div class="macros-card">
                        <strong><i class="fas fa-chart-line"></i> Objectifs macros (estimés)</strong>
                        <div class="macros-badges">
                            <span class="macro-badge">💪 Protéines: <?= $pG ?>g</span>
                            <span class="macro-badge">🍞 Glucides: <?= $cG ?>g</span>
                            <span class="macro-badge">🧈 Lipides: <?= $fG ?>g</span>
                        </div>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 20px;">
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
        
        <footer class="footer">
            <div class="footer-logo">🌿 NutriWise</div>
            <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>
    
    <script>
        (function () {
            const input = document.getElementById('profileImageInput');
            const form = document.getElementById('profileUploadForm');
            const avatarFrame = document.querySelector('.avatar-frame');
            if (!input || !form) return;

            let submitting = false;
            input.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file || submitting) return;

                if (avatarFrame) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        avatarFrame.innerHTML = '<img class="avatar-large" alt="Avatar" src="' + ev.target.result + '">';
                    };
                    reader.readAsDataURL(file);
                }

                submitting = true;
                form.submit();
            });
        })();
    </script>
</body>
</html>