<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FileCheckedInOut extends Notification
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm']; // Add fcm to the channels
    }

    public function toFcm($notifiable)
    {
        // Send notification to FCM (this is where the logic for sending to Firebase comes in)
        $fcmToken = $notifiable->fcm_token;
        $this->sendFcmNotification($fcmToken);
    }

    private function sendFcmNotification($fcmToken)
    {
        $firebase_url = 'https://fcm.googleapis.com/fcm/send';
        $firebase_key = env('FIREBASE_SERVER_KEY'); // Your FCM server key

        $fields = [
            'to' => $fcmToken,
            'notification' => [
                'title' => 'File Check-In/Out',
                'body' => $this->message,
                'sound' => 'default',
            ],
        ];

        $headers = [
            'Authorization: key=' . $firebase_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $firebase_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
