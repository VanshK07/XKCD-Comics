
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'xyz@gmail.com'); //Enter your email id
define('SMTP_PASSWORD', 'app_password');  //Enter your app password
define('FROM_EMAIL', 'xyz@gmail.com');  //enter mail again
define('FROM_NAME', 'XKCD Comics');

function generateVerificationCode() {
    return sprintf('%06d', mt_rand(0, 999999));
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? array_filter(explode("\n", trim(file_get_contents($file)))) : [];
    if (!in_array($email, $emails)) {
        $emails[] = $email;
        file_put_contents($file, implode("\n", $emails) . "\n");
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (file_exists($file)) {
        $emails = array_filter(explode("\n", trim(file_get_contents($file))));
        $emails = array_filter($emails, fn($e) => $e !== $email);
        file_put_contents($file, implode("\n", $emails) . "\n");
    }
}

function sendEmailPHPMailer($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    return sendEmailPHPMailer($email, $subject, $message);
}

function verifyCode(string $email, string $code): bool {
    $codeFile = __DIR__ . '/tmp/codes.json';
    if (!file_exists($codeFile)) return false;

    $codes = json_decode(file_get_contents($codeFile), true);
    if (!isset($codes[$email])) return false;

    return $codes[$email] === $code;
}

function sendUnsubscribeConfirmationEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    return sendEmailPHPMailer($email, $subject, $message);
}

// Combined fetch and format function for XKCD comic
function fetchAndFormatXKCDData() {
    $url  = 'https://xkcd.com/info.0.json';
    $json = @file_get_contents($url);
    if (!$json) {
        return "<p>Failed to fetch XKCD comic.</p>";
    }

    $data = json_decode($json, true);
    if (empty($data) || !isset($data['num'], $data['img'], $data['safe_title'])) {
        return "<p>Invalid XKCD data.</p>";
    }

    $num      = (int) $data['num'];
    $title    = htmlspecialchars($data['safe_title'], ENT_QUOTES);
    $imgUrl   = htmlspecialchars($data['img'], ENT_QUOTES);
    $altText  = htmlspecialchars($data['alt'] ?? '', ENT_QUOTES);
    $comicUrl = "https://xkcd.com/{$num}";

    return <<<HTML
<h2>Today's XKCD Comic: {$title}</h2>
<p>
    <a href="{$comicUrl}" target="_blank" rel="noopener">
        <img src="{$imgUrl}" alt="{$altText}" style="max-width:100%;">
    </a>
</p>
<p><em>{$altText}</em></p>
HTML;
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = array_filter(explode("\n", trim(file_get_contents($file))));
    if (empty($emails)) return;

    $formattedComic = fetchAndFormatXKCDData();
    $unsubscribeUrl = "http://localhost:8000/unsubscribe.php";
    $emailBody = $formattedComic . "<p><a href=\"$unsubscribeUrl\">Unsubscribe</a></p>";
    $subject = "Your Daily XKCD Comic!";

    foreach ($emails as $email) {
        if (!empty(trim($email))) {
            sendEmailPHPMailer(trim($email), $subject, $emailBody);
        }
    }
}
