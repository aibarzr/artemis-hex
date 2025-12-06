<?php

namespace App\Domain\Reporting\Ports;

interface NotificationServiceInterface
{
    public function sendReportGenerated(string $email, string $filePath): void;
}
