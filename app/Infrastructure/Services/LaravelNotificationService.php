<?php

namespace App\Infrastructure\Services;

use App\Domain\Reporting\Ports\NotificationServiceInterface;
use Illuminate\Support\Facades\Mail;

final class LaravelNotificationService implements NotificationServiceInterface
{
    public function sendReportReady(string $email, string $filePath): void
    {
        Mail::raw(
            "Your Excel report has been generated successfully.\n\nFile path: {$filePath}\n\nThank you!",
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Excel Report Generated');
            }
        );
    }

    public function send(string $email, string $subject, string $message): void
    {
        Mail::raw($message, function ($mail) use ($email, $subject) {
            $mail->to($email)
                ->subject($subject);
        });
    }
}
