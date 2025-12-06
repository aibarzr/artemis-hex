<?php

namespace App\Infrastructure\Services;

use App\Domain\Shared\Ports\TransactionServiceInterface;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionService implements TransactionServiceInterface
{
    public function execute(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
