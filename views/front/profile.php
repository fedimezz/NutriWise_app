<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/style.css">
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
            display: grid;
            place-items: center;
        }
        .avatar-large {
            width: 100%;
            height: 100%;
            border-radius: 0;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .avatar-fallback {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            font-size: 3.25rem;
            line-height: 1;
        }
        .upload-btn {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            font-size: 0.9rem;
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
            font-weight: 500;
            color: #2c3e2f;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
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
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <?php
                    $hasProfileImage = !empty($userData['profile_image']);
                    $profileImage = $hasProfileImage ? 'views/assets/uploads/' . $userData['profile_image'] : null;
                    ?>
                    <div class="avatar-frame">
                        <?php if($profileImage): ?>
                            <img src="<?= htmlspecialchars($profileImage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="Avatar" class="avatar-large"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;>👤</div>';">
                        <?php else: ?>
                            <div class="avatar-fallback">👤</div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="upload-form" id="profileUploadForm" novalidate>
                        <input type="hidden" name="action" value="upload_image">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
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
            
            <div class="profile-form-container">
                <h1>Informations personnelles</h1>

                <div style="margin: 10px 0 18px;">
                    <a href="index.php?page=change_password" style="display:inline-block; padding:10px 14px; border-radius:12px; border:1px solid #e9ecef; background:#fff; text-decoration:none; color:#2c3e2f; font-weight:600;">
                        🔒 Changer mon mot de passe
                    </a>
                </div>
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="profile-form" novalidate>
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" value="<?= htmlspecialchars($userData['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" name="telephone" value="<?= htmlspecialchars($userData['telephone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Âge</label>
                            <input type="number" name="age" value="<?= $userData['age'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Genre</label>
                            <select name="gender">
                                <option value="male" <?= ($userData['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Homme</option>
                                <option value="female" <?= ($userData['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Femme</option>
                                <option value="other" <?= ($userData['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Taille (cm)</label>
                            <input type="number" name="taille" step="1" value="<?= $userData['taille'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Poids (kg)</label>
                            <input type="number" name="poids" step="0.1" value="<?= $userData['poids'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Objectif</label>
                            <select name="objectif">
                                <option value="Perte de poids" <?= ($userData['objectif'] ?? '') == 'Perte de poids' ? 'selected' : '' ?>>Perte de poids</option>
                                <option value="Maintien" <?= ($userData['objectif'] ?? '') == 'Maintien' ? 'selected' : '' ?>>Maintien</option>
                                <option value="Prise de muscle" <?= ($userData['objectif'] ?? '') == 'Prise de muscle' ? 'selected' : '' ?>>Prise de muscle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Niveau d'activité</label>
                            <select name="activity_level">
                                <option value="sedentary" <?= ($userData['activity_level'] ?? '') == 'sedentary' ? 'selected' : '' ?>>Sédentaire</option>
                                <option value="light" <?= ($userData['activity_level'] ?? '') == 'light' ? 'selected' : '' ?>>Léger</option>
                                <option value="moderate" <?= ($userData['activity_level'] ?? '') == 'moderate' ? 'selected' : '' ?>>Modéré</option>
                                <option value="active" <?= ($userData['activity_level'] ?? '') == 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="very_active" <?= ($userData['activity_level'] ?? '') == 'very_active' ? 'selected' : '' ?>>Très actif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Allergies / Intolérances (optionnel)</label>
                        <input type="text" name="allergies" value="<?= htmlspecialchars($userData['allergies'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="Ex: arachides, gluten, lactose...">
                        <small style="display:block; margin-top:6px; color:#6c757d;">
                            Astuce: ajoutez des mots-clés séparés par des virgules. Ces infos serviront aux suggestions de repas.
                        </small>
                    </div>

                    <?php
                        // Macro targets (simple, goal-based)
                        $cals = (int)($userData['daily_calories_needs'] ?? 2000);
                        $goal = (string)($userData['objectif'] ?? 'Maintien');
                        if ($goal === 'Perte de poids') { $pPct = 0.30; $cPct = 0.40; $fPct = 0.30; }
                        elseif ($goal === 'Prise de muscle') { $pPct = 0.30; $cPct = 0.50; $fPct = 0.20; }
                        else { $pPct = 0.25; $cPct = 0.50; $fPct = 0.25; }
                        $pG = (int)round(($cals * $pPct) / 4);
                        $cG = (int)round(($cals * $cPct) / 4);
                        $fG = (int)round(($cals * $fPct) / 9);
                    ?>
                    <div style="margin: 14px 0; padding: 14px; border-radius: 16px; background:#f8f9fa;">
                        <strong style="display:block; color:#2e7d32; margin-bottom:6px;">Objectifs macros (estimés)</strong>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <span style="background:#fff; padding:8px 12px; border-radius:999px; border:1px solid #e9ecef;">Protéines: <?= $pG ?>g</span>
                            <span style="background:#fff; padding:8px 12px; border-radius:999px; border:1px solid #e9ecef;">Glucides: <?= $cG ?>g</span>
                            <span style="background:#fff; padding:8px 12px; border-radius:999px; border:1px solid #e9ecef;">Lipides: <?= $fG ?>g</span>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">💾 Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
        
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <span class="logo-icon">🌿</span>
                    <span>NutriWise</span>
                </div>
                <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
            </div>
        </footer>
    </div>
    
    <script>
        // Preview + submit ONCE (avoid double-trigger)
        (function () {
            const input = document.getElementById('profileImageInput');
            const form = document.getElementById('profileUploadForm');
            const avatarFrame = document.querySelector('.avatar-frame');
            if (!input || !form) return;

            let submitting = false;
            input.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file || submitting) return;

                // Preview
                if (avatarFrame) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        avatarFrame.innerHTML = '<img class="avatar-large" alt="Avatar">';
                        const img = avatarFrame.querySelector('img');
                        if (img) img.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                }

                // Submit once
                submitting = true;
                form.submit();
            });
        })();
    </script>
</body>
</html>