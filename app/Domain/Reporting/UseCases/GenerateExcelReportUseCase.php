<?php

namespace App\Domain\Reporting\UseCases;

use App\Domain\Application\DTOs\ConsolidatedListResult;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;

final readonly class GenerateExcelReportUseCase
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function execute(
        array $filters = [],
        string $orderBy = 'years_of_experience',
        string $orderDirection = 'desc',
    ): ConsolidatedListResult {
        return $this->applicationRepository->getConsolidatedList(
            filters: $filters,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            perPage: 50,
            page: 1,
        );
    }
}
