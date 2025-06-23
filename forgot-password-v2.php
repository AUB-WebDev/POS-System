<?php
session_start();
require __DIR__.'/vendor/autoload.php';
require('UI/connectdb.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn_submit'])) {
        handleEmailSubmission($pdo);
    } elseif (isset($_POST['btn_verify'])) {
        handleCodeVerification($pdo);
    } elseif (isset($_POST['btn_reset'])) {
        handlePasswordReset($pdo);
    }
}

function handleEmailSubmission($pdo) {
    $email = $_POST['email'];

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setAlert('Invalid email format', 'error');
        return;
    }

    // Check if email exists
    $query = "SELECT * FROM tbl_user WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        setAlert('Email not found in our system', 'error');
        return;
    }

    // Generate and store verification code
    $verification_code = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", time() + 300); // 5 minutes

    $update_query = "UPDATE tbl_user SET reset_code = ?, reset_expiry = ? WHERE email = ?";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([$verification_code, $expiry, $email]);

    // Send email with PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configure SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], 'Reset Password Code');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body    = "Your verification code is: <strong>$verification_code</strong><br>This code will expire in 5 minutes.";

        $mail->send();

        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_step'] = 'verify_code';
        $_SESSION['verification_attempts'] = 0; // Initialize attempt counter
        setAlert('Verification code sent to your email', 'success');

    } catch (Exception $e) {
        setAlert("Failed to send verification email. Please try again later.", 'error');
        error_log("Mailer Error: " . $e->getMessage());
        echo "<script> console.lgo(".$e->getMessage().")</script>" ;
    }
}

function handleCodeVerification($pdo) {
    // Check if email is in session
    if (!isset($_SESSION['reset_email'])) {
        setAlert('Session expired. Please start again.', 'error');
        header("Location: forgot-password-v2.php");
        exit();
    }

    $code = $_POST['code'];
    $email = $_SESSION['reset_email'];

    // Increment attempt counter
    $_SESSION['verification_attempts'] = ($_SESSION['verification_attempts'] ?? 0) + 1;

    // Check if exceeded max attempts
    if ($_SESSION['verification_attempts'] > 3) {
        session_unset();
        session_destroy();
        setAlert('Too many attempts. Please start again.', 'error');
        header("Location: forgot-password-v2.php");
        exit();
    }

    // Check if code is valid and not expired
    $query = "SELECT * FROM tbl_user WHERE email = ? AND reset_code = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email, $code]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['reset_step'] = 'reset_password';
        $_SESSION['verified'] = true;
        unset($_SESSION['verification_attempts']); // Clear attempt counter
        // No need to redirect - will show the password reset form
    } else {
        setAlert('Invalid or expired verification code', 'error');
        // Stay on the same page to try again
    }
}

function handlePasswordReset($pdo) {
    // Verify session state first
    if (!isset($_SESSION['verified']) || !$_SESSION['verified']) {
        session_unset();
        session_destroy();
        setAlert('Session expired. Please start again.', 'error');
        header("Location: forgot-password-v2.php");
        exit();
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    // Validate passwords
    if ($password !== $confirm_password) {
        setAlert('Passwords do not match', 'error');
        return;
    }

//    if (strlen($password) < 8) {
//        setAlert('Password must be at least 8 characters', 'error');
//        return;
//    }

    // Update password and clear reset fields
//    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE tbl_user SET password = ?, reset_code = NULL, reset_expiry = NULL WHERE email = ?";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$password, $email])) {
        // Clear session
        session_unset();
        session_destroy();

        setAlert('Password updated successfully. You can now login.', 'success');
        header("Location: index.php");
        exit();
    } else {
        setAlert('Failed to reset password', 'error');
    }
}

function setAlert($message, $type) {
    $_SESSION['status'] = $message;
    $_SESSION['status_code'] = $type;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POSBarcode | Forgot Password</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css"
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="index.php" class="h1"><b>POS</b>Barcode</a>
        </div>
        <div class="card-body">
            <?php if (!isset($_SESSION['reset_step'])): ?>
                <!-- Step 1: Request Code -->
                <h4 class="login-box-msg"><b>Forgot Password</b></h4>
                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="btn_submit" class="btn btn-primary btn-block">Request Code</button>
                        </div>
                    </div>
                </form>

            <?php elseif ($_SESSION['reset_step'] === 'verify_code'): ?>
                <!-- Step 2: Verify Code -->
                <h4 class="login-box-msg"><b>Enter Verification Code</b></h4>
                <p>Please check your mail for the verification code!</p>
                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Verification Code" name="code" required maxlength="6">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="btn_verify" class="btn btn-primary btn-block">Verify Code</button>
                        </div>
                    </div>
                </form>

            <?php elseif ($_SESSION['reset_step'] === 'reset_password'): ?>
                <!-- Step 3: Reset Password -->
                <h4 class="login-box-msg"><b>Reset Password</b></h4>
                <form action="" method="POST">
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="New Password" name="password" required >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="btn_reset" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <p class="mt-3 mb-1">
                <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
    $(function() {
        // Show alerts from PHP
        <?php if (isset($_SESSION['status']) && $_SESSION['status_code'] != ""): ?>
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        Toast.fire({
            icon: '<?php echo $_SESSION['status_code']; ?>',
            title: '<?php echo addslashes($_SESSION['status']); ?>'
        });
        <?php
        unset($_SESSION['status']);
        unset($_SESSION['status_code']);
        ?>
        <?php endif; ?>

        // For password reset step, show success message in a popup
        <?php if (isset($_SESSION['verified']) && $_SESSION['verified']): ?>
        Swal.fire({
            icon: 'success',
            title: 'Code Verified',
            text: 'You can now set your new password',
            confirmButtonText: 'Continue'
        });
        <?php endif; ?>
    });
</script>
</body>
</html>