<?php
// Simple Mail Service using PHP's mail function
// Can be easily replaced with PHPMailer in the future

class MailService {
    private $fromEmail = 'noreply@nutriwise.com';
    private $fromName = 'NutriWise';
    
    public function __construct() {
        // Configure if needed for SMTP support
    }
    
    public function sendConsultationEmail($userEmail, $userName, $consultationData) {
        try {
            if (empty($userEmail)) {
                return false;
            }
            
            $subject = "Votre consultation NutriWise - " . date('d/m/Y', strtotime($consultationData['date']));
            
            $htmlMessage = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .header { background: #2e7d32; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
        .detail { margin: 15px 0; padding: 10px; background: #f0f7f0; border-left: 4px solid #2e7d32; border-radius: 4px; }
        .detail strong { color: #2e7d32; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
        .button { display: inline-block; background: #2e7d32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🌿 NutriWise</h1>
            <p>Votre consultation</p>
        </div>
        <div class='content'>
            <p>Bonjour <strong>" . htmlspecialchars($userName) . "</strong>,</p>
            <p>Voici le résumé de votre consultation :</p>
            
            <div class='detail'>
                <strong>📅 Date :</strong> " . date('d/m/Y', strtotime($consultationData['date'])) . "
            </div>
            
            <div class='detail'>
                <strong>📝 Remarque :</strong><br>" . nl2br(htmlspecialchars($consultationData['remarque'])) . "
            </div>
            
            <div class='detail'>
                <strong>💡 Conseil :</strong><br>" . nl2br(htmlspecialchars($consultationData['conseil'])) . "
            </div>";
            
            if (!empty($consultationData['poids_cible'])) {
                $htmlMessage .= "
            <div class='detail'>
                <strong>⚖️ Poids cible :</strong> " . htmlspecialchars($consultationData['poids_cible']) . " kg
            </div>";
            }
            
            $htmlMessage .= "
            <p style='margin-top: 30px; color: #666;'>Continuez vos efforts ! Vous êtes sur la bonne voie 💪</p>
            
            <div class='footer'>
                <p>L'équipe NutriWise<br>Votre partenaire santé et nutrition</p>
            </div>
        </div>
    </div>
</body>
</html>";
            
            $headers = "From: " . $this->fromEmail . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($userEmail, $subject, $htmlMessage, $headers);
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendSuiviEmail($userEmail, $userName, $suiviData) {
        try {
            if (empty($userEmail)) {
                return false;
            }
            
            $subject = "Résumé de votre suivi NutriWise - " . date('d/m/Y', strtotime($suiviData['date']));
            
            $htmlMessage = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .header { background: #2e7d32; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
        .detail { margin: 15px 0; padding: 10px; background: #f0f7f0; border-left: 4px solid #2e7d32; border-radius: 4px; }
        .detail strong { color: #2e7d32; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🌿 NutriWise</h1>
            <p>Votre suivi quotidien</p>
        </div>
        <div class='content'>
            <p>Bonjour <strong>" . htmlspecialchars($userName) . "</strong>,</p>
            <p>Votre suivi a été enregistré avec succès. Voici le résumé :</p>
            
            <div class='detail'>
                <strong>📅 Date :</strong> " . date('d/m/Y', strtotime($suiviData['date'])) . "
            </div>
            
            <div class='detail'>
                <strong>⚖️ Poids :</strong> " . htmlspecialchars($suiviData['poids']) . " kg
            </div>
            
            <div class='detail'>
                <strong>🔥 Calories :</strong> " . htmlspecialchars($suiviData['calories']) . " kcal/jour
            </div>
            
            <div class='detail'>
                <strong>📊 État :</strong> " . htmlspecialchars($suiviData['etat']) . "
            </div>";
            
            if (isset($suiviData['eau_bue']) && $suiviData['eau_bue'] !== '') {
                $htmlMessage .= "
            <div class='detail'>
                <strong>💧 Eau bue :</strong> " . htmlspecialchars($suiviData['eau_bue']) . " L
            </div>";
            }
            
            if (isset($suiviData['etat_du_jour']) && $suiviData['etat_du_jour'] !== '') {
                $htmlMessage .= "
            <div class='detail'>
                <strong>😊 État du jour :</strong> " . htmlspecialchars($suiviData['etat_du_jour']) . "
            </div>";
            }
            
            if (isset($suiviData['jour_reussi'])) {
                $reussi_text = $suiviData['jour_reussi'] ? 'Oui 🌟' : 'Non';
                $htmlMessage .= "
            <div class='detail'>
                <strong>🎯 Jour réussi :</strong> " . $reussi_text . "
            </div>";
            }
            
            $htmlMessage .= "
            <p style='margin-top: 30px; color: #666;'>Continuez comme ça ! Chaque jour compte 💪</p>
            
            <div class='footer'>
                <p>L'équipe NutriWise<br>Votre partenaire santé et nutrition</p>
            </div>
        </div>
    </div>
</body>
</html>";
            
            $headers = "From: " . $this->fromEmail . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            return mail($userEmail, $subject, $htmlMessage, $headers);
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }
}
?>
