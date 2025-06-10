<?php
session_start();
require_once 'functions.php';

$message = '';
$step = 'email'; // email or verification

// Handle email submission
if (isset($_POST['email']) && !empty($_POST['email']) && !isset($_POST['verification_code'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        $code = generateVerificationCode();
        $_SESSION['verification_code'] = $code;
        $_SESSION['pending_email'] = $email;
        
        if (sendVerificationEmail($email, $code)) {
            $message = "Verification code sent to your email!";
            $step = 'verification';
        } else {
            $message = "Failed to send verification email. Please try again.";
        }
    } else {
        $message = "Please enter a valid email address.";
    }
}

// Handle verification code submission
if (isset($_POST['verification_code']) && !empty($_POST['verification_code'])) {
    $inputCode = $_POST['verification_code'];
    $storedCode = $_SESSION['verification_code'] ?? '';
    $email = $_SESSION['pending_email'] ?? '';
    
    if ($inputCode === $storedCode && !empty($email)) {
        registerEmail($email);
        $message = "Email successfully registered! You will receive XKCD Comics updates.";
        
        // Clear session data
        unset($_SESSION['verification_code']);
        unset($_SESSION['pending_email']);
        $step = 'email';
    } else {
        $message = "Invalid verification code. Please try again.";
        $step = 'verification';
    }
}

// Keep verification step if we have pending verification
if (isset($_SESSION['pending_email']) && isset($_SESSION['verification_code'])) {
    $step = 'verification';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XKCD Comics Subscription</title>
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
            background-color: #007cba;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #005a87;
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
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #007cba;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>XKCD Comics Subscription</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Email Registration Form - Always visible -->
        <form method="POST" action="">
            <div class="step-info">
                <strong>Step 1:</strong> Enter your email to receive XKCD Comics updates
            </div>
            
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" name="email" id="email" required 
                       value="<?php echo isset($_SESSION['pending_email']) ? htmlspecialchars($_SESSION['pending_email']) : ''; ?>">
            </div>
            
            <button type="submit" id="submit-email">Submit</button>
        </form>

        <!-- Verification Code Form - Always visible -->
        <form method="POST" action="" style="margin-top: 30px;">
            <div class="step-info">
                <strong>Step 2:</strong> Enter the 6-digit verification code sent to your email
            </div>
            
            <div class="form-group">
                <label for="verification_code">Verification Code:</label>
                <input type="text" name="verification_code" id="verification_code" 
                       maxlength="6" required pattern="[0-9]{6}" 
                       placeholder="Enter 6-digit code">
            </div>
            
            <button type="submit" id="submit-verification">Verify</button>
        </form>

        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #eee;">
            <p>Already subscribed? <a href="unsubscribe.php">Unsubscribe here</a></p>
        </div>
    </div>
</body>
</html>