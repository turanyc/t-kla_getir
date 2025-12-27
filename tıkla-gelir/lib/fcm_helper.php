<?php
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

require_once __DIR__.'/../vendor/autoload.php';   // composer ile
$factory = (new Factory)->withServiceAccount(__DIR__.'/../firebase_credentials.json');
$messaging = $factory->createMessaging();

function sendMulticastFCM(array $tokens, string $title, string $body, array $data = []) {
    global $messaging;
    $message = CloudMessage::new()
        ->withNotification(Notification::create($title, $body))
        ->withData($data);
    $report = $messaging->sendMulticast($message, $tokens);
    return $report->successes()->count();
}