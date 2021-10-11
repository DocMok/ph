<?php

namespace App\Http\Services;

use App\Models\Notice;
use App\Models\NotificationToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{
    public function notificate(User $fromUser, User $toUser, Model $notificateAbout, string $pushTitle, string $pushBody, string $text)
    {

        $notification = Notice::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'text' => $text,
        ]);

        $notificateAbout->notices()->save($notification);
        $this->sendPush($toUser, $pushTitle, $pushBody);
    }

    private function sendPush(User $toUser, string $title, string $body): void
    {
        $notificationTokens = $toUser->notificationTokens;

        if (count($notificationTokens) > 0) {
            $tokens = array_column($notificationTokens->toArray(), 'token');
            $notification = CloudMessage::new()->withNotification([
                'title' => $title,
                'body' => $body,
            ])
                ->withData(['event' => 'new_like']);
            $report = app('firebase.messaging')->sendMulticast($notification, $tokens);

            $badTokens = array_merge($report->unknownTokens(), $report->invalidTokens());

            foreach ($notificationTokens as $notificationToken) {
                if (in_array($notificationToken->token, $badTokens)) {
                    NotificationToken::where('token', $notificationToken->token)->delete();
                }
            }
        }
    }
}
