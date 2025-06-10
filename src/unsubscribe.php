<?php
session_start();
require_once 'functions.php';

$message = '';
$step = 'email';

if (isset($_POST['unsubscribe_email']) && !empty($_POST['unsubscribe_email']) && !isset($_POST['unsubscribe_verification_code'])) {
    $email = filter_var($_POST['unsubscribe_email'], FILTER_VALIDATE_EMAIL);
    if ($email) {

        $file = __DIR__ . '/registered_emails.txt';
        $emailExists = false;
        
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $emails = array_filter(explode("\n", trim($content)));
            $emailExists = in_array($email, $emails);
        }
        
        if ($emailExists) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_verification_code'] = $code;
            $_SESSION['pending_unsubscribe_email'] = $email;
            
            if (sendUnsubscribeConfirmationEmail($email, $code)) {
                $message = "Unsubscribe confirmation code sent to your email!";
                $step = 'verification';
            } else {
                $message = "Failed to send confirmation email. Please try again.";
            }
        } else {
            $message = "Email not found in our subscription list.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}

if (isset($_POST['unsubscribe_verification_code']) && !empty($_POST['unsubscribe_verification_code'])) {
    $inputCode = $_POST['unsubscribe_verification_code'];
    $storedCode = $_SESSION['unsubscribe_verification_code'] ?? '';
    $email = $_SESSION['pending_unsubscribe_email'] ?? '';
    
    if ($inputCode === $storedCode && !empty($email)) {
        unsubscribeEmail($email);
        $message = "Successfully unsubscribed! You will no longer receive XKCD Comics updates.";
        
        unset($_SESSION['unsubscribe_verification_code']);
        unset($_SESSION['pending_unsubscribe_email']);
        $step = 'email';
    } else {
        $message = "Invalid verification code. Please try again.";
        $step = 'verification';
    }
}

if (isset($_SESSION['pending_unsubscribe_email']) && isset($_SESSION['unsubscribe_verification_code'])) {
    $step = 'verification';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - XKCD Comics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #dc3545;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #c82333;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .step-info {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .back-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unsubscribe from XKCD Comics</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false || strpos($message, 'not found') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Unsubscribe Email Form -->
        <form method="POST" action="">
            <div class="step-info">
                <strong>Step 1:</strong> Enter your email address to unsubscribe
            </div>
            
            <div class="form-group">
                <label for="unsubscribe_email">Email Address:</label>
                <input type="email" name="unsubscribe_email" id="unsubscribe_email" required 
                       value="<?php echo isset($_SESSION['pending_unsubscribe_email']) ? htmlspecialchars($_SESSION['pending_unsubscribe_email']) : ''; ?>">
            </div>
            
            <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
        </form>

        <!-- Unsubscribe Verification Code Form -->
        <form method="POST" action="" style="margin-top: 30px;">
            <div class="step-info">
                <strong>Step 2:</strong> Enter the 6-digit confirmation code sent to your email
            </div>
            
            <div class="form-group">
                <label for="unsubscribe_verification_code">Confirmation Code:</label>
                <input type="text" name="unsubscribe_verification_code" id="unsubscribe_verification_code" 
                       maxlength="6" pattern="[0-9]{6}" 
                       placeholder="Enter 6-digit code">
            </div>
            
            <button type="submit" id="verify-unsubscribe">Verify</button>
        </form>

        <div class="back-link">
            <p><a href="index.php">← Back to subscription page</a></p>
        </div>
    </div>
</body>
</html>