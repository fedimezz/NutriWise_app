

## ✨ Fonctionnalités

### 👥 Gestion des Utilisateurs (CRUD complet)
- ✅ Liste paginée avec recherche en temps réel
- ✅ Ajout d'utilisateur avec validation des données
- ✅ Édition des informations personnelles
- ✅ Suppression individuelle et multiple (bulk delete)
- ✅ Calcul automatique de l'IMC (taille/poids)
- ✅ Calcul des besoins caloriques journaliers
- ✅ Gestion des rôles et statuts

### 🥗 Gestion des Aliments (CRUD complet)
- ✅ Catalogue d'aliments avec valeurs nutritionnelles
- ✅ Ajout avec upload d'image ou URL externe
- ✅ Édition des informations nutritionnelles
- ✅ Score écologique et indicateur de durabilité
- ✅ Catégorisation par saison et type

### 🍳 Gestion des Recettes (CRUD avancé)
- ✅ Liste avec filtres par catégorie et recherche
- ✅ Création avec ingrédients dynamiques
- ✅ Étapes de préparation numérotées
- ✅ Système de tags personnalisables
- ✅ Upload d'images haute qualité
- ✅ Vue détaillée publique
- ✅ Système de favoris utilisateur
- ✅ Compteur de vues automatique

### 🔐 Authentification et Sécurité
- ✅ Connexion/déconnexion sécurisée
- ✅ Inscription avec vérification email
- ✅ Récupération de mot de passe (code de vérification)
- ✅ Gestion des rôles hiérarchiques
- ✅ Protection CSRF sur tous les formulaires
- ✅ Sessions PHP sécurisées

### 🎨 Interface Utilisateur
- ✅ Design responsive (mobile-first)
- ✅ Sidebar adaptative
- ✅ Thème vert naturel cohérent
- ✅ Animations et transitions fluides
- ✅ Tables interactives avec tri et filtres

---

## 📁 Structure du Projet

```
nutriwise/
├── index.php                 # Point d'entrée principal
├── controllers/              # Logique métier
│   ├── AuthController.php    # Authentification
│   ├── UserController.php    # Gestion utilisateurs
│   ├── AlimentController.php # Gestion aliments
│   ├── RecetteController.php # Gestion recettes
│   └── AdminController.php   # Panel administration
├── models/                   # Modèles de données
│   ├── Database.php          # Connexion BDD
│   ├── UserModel.php         # Modèle utilisateurs
│   ├── AlimentModel.php      # Modèle aliments
│   ├── RecetteModel.php      # Modèle recettes
│   └── Mailer.php            # Service email
├── views/                    # Templates et vues
│   ├── front/                # Interface publique
│   │   ├── aliments.php      # Liste aliments
│   │   ├── recettes.php      # Liste recettes
│   │   ├── recette_details.php # Détail recette
│   │   ├── login.php         # Connexion
│   │   ├── register.php      # Inscription
│   │   └── partials/         # Composants réutilisables
│   └── back/                 # Interface admin
│       ├── dashboard.php     # Dashboard admin
│       ├── users.php         # Gestion utilisateurs
│       ├── aliments.php      # Gestion aliments
│       └── recettes.php      # Gestion recettes
├── 
```

---

## 🗄️ Schéma de la Base de Données

### Tables Principales

#### `users`
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    taille DECIMAL(5,2), -- en cm
    poids DECIMAL(5,2), -- en kg
    imc DECIMAL(4,2), -- calculé automatiquement
    objectif ENUM('perte', 'maintien', 'prise'),
    activity_level ENUM('sedentaire', 'leger', 'modere', 'actif', 'tres_actif'),
    daily_calories_needs INT,
    profile_image VARCHAR(255),
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    role ENUM('user', 'nutritionist', 'admin', 'owner') DEFAULT 'user',
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `aliments`
```sql
CREATE TABLE aliments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT,
    calories DECIMAL(6,2),
    proteines DECIMAL(5,2),
    glucides DECIMAL(5,2),
    lipides DECIMAL(5,2),
    fibres DECIMAL(5,2),
    eco_score ENUM('A', 'B', 'C', 'D', 'E'),
    durable BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    saison VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `recettes`
```sql
CREATE TABLE recettes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    categorie VARCHAR(50),
    difficulte ENUM('Facile', 'Moyen', 'Difficile'),
    temps_preparation INT, -- en minutes
    temps_cuisson INT, -- en minutes
    portions INT DEFAULT 4,
    image VARCHAR(255),
    tags VARCHAR(255),
    views INT DEFAULT 0,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tables de liaison
- `recette_ingredients` : Lie recettes et ingrédients avec quantités
- `recette_etapes` : Étapes de préparation ordonnées
- `recette_favoris` : Favoris des utilisateurs
- `categories` : Catégories d'aliments
- `roles` : Définition des rôles utilisateur
- `activity_logs` : Journal d'activité

---

## 🌐 URLs Disponibles

### Interface Publique
- `index.php?page=home` → Page d'accueil
- `index.php?page=login` → Connexion
- `index.php?page=register` → Inscription
- `index.php?page=motpasse` → Mot de passe oublié
- `index.php?page=aliments` → Liste des aliments
- `index.php?page=recettes` → Liste des recettes
- `index.php?page=recette_details&id=X` → Détail d'une recette
- `index.php?page=profile` → Profil utilisateur
- `index.php?page=suivi` → Suivi nutritionnel

### Interface Administration
- `index.php?page=admin_dashboard` → Dashboard administrateur
- `index.php?page=admin_users` → Gestion utilisateurs
- `index.php?page=admin_add_user` → Ajout utilisateur
- `index.php?page=admin_edit_user&id=X` → Édition utilisateur
- `index.php?page=admin_delete_user&id=X` → Suppression utilisateur
- `index.php?page=admin_delete_users_bulk` → Suppression multiple
- `index.php?page=admin_aliments` → Gestion aliments
- `index.php?page=admin_add_aliment` → Ajout aliment
- `index.php?page=admin_edit_aliment&id=X` → Édition aliment
- `index.php?page=admin_recettes` → Gestion recettes
- `index.php?page=admin_add_recette` → Ajout recette
- `index.php?page=admin_edit_recette&id=X` → Édition recette

---

## 🔒 Sécurité

### Mesures Implémentées
- **Protection CSRF** : Tokens générés pour chaque formulaire
- **Hachage des mots de passe** : Utilisation de bcrypt
- **Validation des entrées** : Sanitisation et validation côté serveur
- **Sessions sécurisées** : Régénération d'ID et timeout automatique
- **Contrôle d'accès** : Vérification des rôles pour chaque page
- **Upload sécurisé** : Validation des types de fichiers et tailles

### Système de Rôles
1. **Owner** : Accès complet à toutes les fonctionnalités
2. **Admin** : Gestion utilisateurs, aliments, recettes
3. **Nutritionist** : Accès aux données nutritionnelles
4. **User** : Accès limité à l'interface publique

---

## 🔌 API Interne

### Exemples d'utilisation

#### Récupération des notifications
```javascript
fetch('index.php?page=get_notifications')
    .then(response => response.json())
    .then(data => {
        // Traitement des données
    });
```

#### Marquage d'une notification comme lue
```javascript
fetch('index.php?page=mark_notification', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'id=' + notificationId
});
```

#### Recherche d'aliments
```javascript
const searchTerm = 'pomme';
fetch(`index.php?page=search_aliments&term=${encodeURIComponent(searchTerm)}`)
    .then(response => response.json())
    .then(data => {
        // Affichage des résultats
    });
```

---

## 👥 Guide pour l'Équipe

### Ce qui reste à développer

#### 🔄 Priorité Haute
- [ ] **Module Plans nutritionnels** : CRUD complet avec génération automatique
- [ ] **Dashboard avec graphiques** : Statistiques utilisateurs, aliments, recettes
- [ ] **Export des données** : CSV/PDF pour rapports nutritionnels

#### 🔄 Priorité Moyenne
- [ ] **Notifications par email** : Système d'alertes personnalisées
- [ ] **Chatbot intelligent** : IA pour recommandations nutritionnelles
- [ ] **API externe** : Connexion à des bases de données nutritionnelles

#### 🔄 Priorité Basse
- [ ] **Application mobile** : Version React Native
- [ ] **Multilingue** : Support anglais/espagnol
- [ ] **Mode hors ligne** : Synchronisation des données

### Bonnes Pratiques
- Toujours utiliser les fonctions de sanitisation pour les entrées utilisateur
- Tester les nouvelles fonctionnalités sur différents navigateurs
- Documenter les nouvelles méthodes dans les modèles
- Respecter la structure MVC existante

---




---

## 📞 Contact et Licence

### Équipe de Développement
- **Développeur Principal** : [Votre Nom] - [votre.email@exemple.com]
- **Repository GitHub** : [https://github.com/votre-username/nutriwise](https://github.com/votre-username/nutriwise)

### Licence
Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

### Contribution
Les contributions sont les bienvenues ! Veuillez lire [CONTRIBUTING.md](CONTRIBUTING.md) pour les directives.

---

*Dernière mise à jour : Mai 2026*</content>
<parameter name="filePath">c:\xampp\htdocs\nutriwise_app\README.md