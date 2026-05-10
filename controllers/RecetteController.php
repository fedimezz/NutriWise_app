<?php

class ChatbotController {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */

    public function api() {

        header('Content-Type: application/json; charset=utf-8');

        try {

            $data = json_decode(file_get_contents("php://input"), true);

            $message = trim(strtolower($data['message'] ?? ''));

            if (!$message) {
                echo json_encode([
                    'success' => false,
                    'response' => 'Veuillez entrer un message.'
                ]);
                exit;
            }

            $response = $this->handleMessage($message);

            echo json_encode([
                'success' => true,
                'response' => $response
            ]);

        } catch (Throwable $e) {

            echo json_encode([
                'success' => false,
                'response' => 'Erreur serveur.',
                'error' => $e->getMessage()
            ]);
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN LOGIC
    |--------------------------------------------------------------------------
    */

    private function handleMessage(string $message): string {

        // BONJOUR

        if (
            str_contains($message, 'bonjour') ||
            str_contains($message, 'salut')
        ) {

            return "
            👋 Bonjour !

            Je suis l'assistant NutriWise 🥗

            Je peux :
            • Trouver des recettes
            • Donner les calories
            • Afficher les ingrédients
            • Suggérer des plats rapides
            • Trouver des recettes végétariennes
            ";
        }

        // RECETTES

        if (
            str_contains($message, 'recette') ||
            str_contains($message, 'plat')
        ) {

            return $this->searchRecipes($message);
        }

        // CALORIES / NUTRITION

        if (
            str_contains($message, 'calorie') ||
            str_contains($message, 'nutrition') ||
            str_contains($message, 'protéine') ||
            str_contains($message, 'glucide')
        ) {

            return $this->searchFood($message);
        }

        return "
        🤔 Je n'ai pas compris.

        Exemples :
        • Recette végétarienne
        • Calories banane
        • Recette rapide
        • Recette petit-déjeuner
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | RECIPES
    |--------------------------------------------------------------------------
    */

    private function searchRecipes(string $message): string {

        $sql = "
            SELECT *
            FROM recettes
            WHERE is_public = 1
        ";

        $params = [];

        // catégorie

        if (str_contains($message, 'petit')) {
            $sql .= " AND categorie = :cat";
            $params[':cat'] = 'Petit-déjeuner';
        }

        if (str_contains($message, 'dessert')) {
            $sql .= " AND categorie = :cat2";
            $params[':cat2'] = 'Dessert';
        }

        // rapide

        if (
            str_contains($message, 'rapide') ||
            str_contains($message, 'vite')
        ) {

            $sql .= "
                AND (temps_preparation + temps_cuisson) <= 30
            ";
        }

        // végétarien

        if (
            str_contains($message, 'végétarien') ||
            str_contains($message, 'vegetarien')
        ) {

            $sql .= "
                AND tags LIKE :veg
            ";

            $params[':veg'] = '%légumes%';
        }

        $sql .= "
            ORDER BY views DESC
            LIMIT 5
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$recipes) {

            return "😕 Aucune recette trouvée.";
        }

        $response = "🍽️ Recettes trouvées :<br><br>";

        foreach ($recipes as $recipe) {

            $time =
                (int)$recipe['temps_preparation']
                +
                (int)$recipe['temps_cuisson'];

            $response .= "
                <div style='margin-bottom:15px'>
                    <strong>
                        {$recipe['nom']}
                    </strong><br>

                    📂 {$recipe['categorie']}<br>

                    ⏱️ {$time} min<br>

                    ⭐ {$recipe['difficulte']}<br>

                    📝 {$recipe['description']}
                </div>
            ";
        }

        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | FOOD INFO
    |--------------------------------------------------------------------------
    */

    private function searchFood(string $message): string {

        $food = $this->extractFoodName($message);

        if (!$food) {
            return "🥑 Quel aliment ?";
        }

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM aliments
            WHERE LOWER(nom) LIKE :food
            LIMIT 1
        ");

        $stmt->execute([
            ':food' => '%' . strtolower($food) . '%'
        ]);

        $aliment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aliment) {

            return "😕 Aliment non trouvé.";
        }

        return "
        🥗 <strong>{$aliment['nom']}</strong><br><br>

        🔥 Calories : {$aliment['calories']} kcal<br>

        💪 Protéines : {$aliment['proteines']} g<br>

        🍞 Glucides : {$aliment['glucides']} g<br>

        🧈 Lipides : {$aliment['lipides']} g<br>

        🌾 Fibres : {$aliment['fibres']} g<br>

        🌱 Eco score : {$aliment['eco_score']}
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT FOOD
    |--------------------------------------------------------------------------
    */

    private function extractFoodName(string $message): ?string {

        $foods = [

            'banane',
            'avocat',
            'pomme',
            'brocoli',
            'boeuf',
            'poulet',
            'saumon',
            'riz',
            'oeuf',
            'fromage',
            'carotte'
        ];

        foreach ($foods as $food) {

            if (str_contains($message, $food)) {
                return $food;
            }
        }

        return null;
    }
}