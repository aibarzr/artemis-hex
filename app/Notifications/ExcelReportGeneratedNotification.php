<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExcelReportGeneratedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $filePath,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Excel Report Generated Successfully')
            ->greeting('Hello!')
            ->line('Your Excel report has been generated successfully.')
            ->line('File location: '.$this->filePath)
            ->line('The report contains the consolidated list of applications with their assigned evaluators.')
            ->line('Thank you for using our application!');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'file_path' => $this->filePath,
        ];
    }
}
