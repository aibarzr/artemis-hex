<?php

namespace App\Domain\Application\UseCases;

use App\Domain\Application\DTOs\ConsolidatedListResult;
use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Shared\Ports\CacheServiceInterface;

final readonly class GetConsolidatedListUseCase
{
    private const CACHE_TTL = 300; // 5 minutes

    private const CACHE_PREFIX = 'consolidated_list';

    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private CacheServiceInterface $cacheService,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function execute(
        array $filters = [],
        string $orderBy = 'years_of_experience',
        string $orderDirection = 'desc',
        int $perPage = 15,
        int $page = 1,
    ): ConsolidatedListResult {
        $cacheKey = $this->generateCacheKey(
            filters: $filters,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            perPage: $perPage,
            page: $page,
        );

        return $this->cacheService->remember(
            key: $cacheKey,
            ttl: self::CACHE_TTL,
            callback: fn () => $this->applicationRepository->getConsolidatedList(
                filters: $filters,
                orderBy: $orderBy,
                orderDirection: $orderDirection,
                perPage: $perPage,
                page: $page,
            )
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function generateCacheKey(
        array $filters,
        string $orderBy,
        string $orderDirection,
        int $perPage,
        int $page,
    ): string {
        $params = [
            'filters' => $filters,
            'order_by' => $orderBy,
            'order_direction' => $orderDirection,
            'per_page' => $perPage,
            'page' => $page,
        ];

        return self::CACHE_PREFIX.':'.md5(json_encode($params));
    }
}
