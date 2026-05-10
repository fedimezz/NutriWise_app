<?php
declare(strict_types=1);

if (class_exists('ChatbotController', false)) {
    return;
}

class ChatbotController {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * API endpoint chatbot
     */
    public function api(): void {

        header('Content-Type: application/json; charset=utf-8');

        try {

            $input = json_decode(file_get_contents('php://input'), true);

            $message = strtolower(trim($input['message'] ?? ''));

            if ($message === '') {
                echo json_encode([
                    'response' => 'Veuillez poser une question.'
                ]);
                return;
            }

            $response = $this->processMessage($message);

            echo json_encode([
                'response' => $response
            ]);

        } catch (Throwable $e) {

            echo json_encode([
                'response' => '❌ Erreur serveur chatbot.',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Router simple des messages
     */
    private function processMessage(string $message): string {

        if (str_contains($message, 'recette') || str_contains($message, 'plat')) {
            return $this->searchRecipes($message);
        }

        if (
            str_contains($message, 'calorie') ||
            str_contains($message, 'kcal') ||
            str_contains($message, 'nutrition')
        ) {
            return $this->searchFood($message);
        }

        if (str_contains($message, 'bonjour') || str_contains($message, 'salut')) {
            return "👋 Bonjour ! Je suis NutriWise Assistant.";
        }

        return "🤔 Je n'ai pas compris. Essayez : recette, calories banane, etc.";
    }

    /**
     * Search recipes
     */
    private function searchRecipes(string $message): string {

        try {

            $sql = "SELECT nom, description, temps_preparation, temps_cuisson
                    FROM recettes
                    LIMIT 5";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$recettes) {
                return "😕 Aucune recette trouvée.";
            }

            $response = "🍽️ Recettes :\n\n";

            foreach ($recettes as $r) {

                $response .= "• " . $r['nom'] . "\n";
                $response .= "  ⏱ " . ($r['temps_preparation'] + $r['temps_cuisson']) . " min\n\n";
            }

            return $response;

        } catch (Throwable $e) {
            return "❌ Erreur recettes.";
        }
    }

    /**
     * Search food nutrition
     */
    private function searchFood(string $message): string {

        try {

            $words = explode(' ', $message);
            $food = end($words);

            $sql = "SELECT nom, calories, proteines, glucides, lipides
                    FROM aliments
                    WHERE LOWER(nom) LIKE :nom
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => '%' . $food . '%'
            ]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return "😕 Aliment non trouvé.";
            }

            return "🥗 " . $data['nom'] . "\n"
                . "🔥 Calories: " . $data['calories'] . " kcal\n"
                . "💪 Protéines: " . $data['proteines'] . " g\n"
                . "🍞 Glucides: " . $data['glucides'] . " g\n"
                . "🧈 Lipides: " . $data['lipides'] . " g";

        } catch (Throwable $e) {
            return "❌ Erreur nutrition.";
        }
    }
}