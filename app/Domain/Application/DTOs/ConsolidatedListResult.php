<?php

namespace App\Domain\Application\DTOs;

final readonly class ConsolidatedListResult
{
    /**
     * @param  array<ConsolidatedListItem>  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(fn (ConsolidatedListItem $item) => $item->toArray(), $this->items),
            'meta' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
            ],
        ];
    }
}
