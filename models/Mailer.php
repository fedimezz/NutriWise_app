<?php
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';

class Mailer {

    public static function sendCode($email, $code) {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'YOUR_EMAIL@gmail.com';
        $mail->Password = 'APP_PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('YOUR_EMAIL@gmail.com', 'NutriWise');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your account';

        $mail->Body = "
        <div style='font-family:sans-serif'>
            <h2>Email Verification</h2>
            <p>Your verification code:</p>
            <h1 style='letter-spacing:5px'>$code</h1>
            <p>This code expires in 10 minutes.</p>
        </div>
        ";

        return $mail->send();
    }
}