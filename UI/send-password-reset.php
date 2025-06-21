<?php

require('connection.php');
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE tbl_user
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $pdo->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

if ($stmt->execute()) {
  $mail = new PHPMailer(true);
  $mail = SMTP
  $mail = isSMTP();

  $mail->setFrom("noreply@example.com");
  $mail->addAddress($email);
  $mail->Subject = "Password Reset";
  $mail->Body = <<<END

    Click <a href="http://mypos.local/UI/reset-password.php?token=$token">here</a>
    to reset your password.

    END;

  try {

    $mail->send();

  } catch (Exception $e) {

    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

  }

}

echo "Message sent, please check your inbox.";
