<?php

namespace App\Jobs;

use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Reporting\Ports\NotificationServiceInterface;
use App\Domain\Reporting\Ports\ReportGeneratorInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateExcelReportJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public int $uniqueFor = 3600;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        private readonly array $filters,
        private readonly string $orderBy,
        private readonly string $orderDirection,
        private readonly string $notificationEmail,
    ) {
        $this->onQueue('reports');
    }

    public function uniqueId(): string
    {
        return md5(json_encode([
            'filters' => $this->filters,
            'order_by' => $this->orderBy,
            'order_direction' => $this->orderDirection,
            'email' => $this->notificationEmail,
        ]));
    }

    public function handle(
        ApplicationRepositoryInterface $applicationRepository,
        ReportGeneratorInterface $reportGenerator,
        NotificationServiceInterface $notificationService,
    ): void {
        $consolidatedList = $applicationRepository->getConsolidatedList(
            filters: $this->filters,
            orderBy: $this->orderBy,
            orderDirection: $this->orderDirection,
            perPage: 50,
            page: 1,
        );

        $filePath = $reportGenerator->generateExcel($consolidatedList);

        if ($this->notificationEmail !== '') {
            $notificationService->sendReportReady($this->notificationEmail, $filePath);
        }
    }
}
