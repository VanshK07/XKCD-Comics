<?php
// cron.php - Handles sending XKCD comic via email

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/functions.php';

$logFile = __DIR__ . '/cron.log';
$timestamp = date('Y-m-d H:i:s');

try {
    sendXKCDUpdatesToSubscribers();
    file_put_contents($logFile, "[$timestamp] CRON executed successfully - XKCD comic sent\n", FILE_APPEND | LOCK_EX);
    echo "CRON job executed successfully at $timestamp\n";
} catch (Exception $e) {
    file_put_contents($logFile, "[$timestamp] CRON error: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
    echo "CRON job failed at $timestamp: " . $e->getMessage() . "\n";
}
