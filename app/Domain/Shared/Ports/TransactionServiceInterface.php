<?php

namespace App\Domain\Shared\Ports;

interface TransactionServiceInterface
{
    /**
     * Execute a callback within a database transaction.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     *
     * @throws \Throwable
     */
    public function execute(callable $callback): mixed;
}
