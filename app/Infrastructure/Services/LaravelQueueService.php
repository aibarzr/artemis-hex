<?php

namespace App\Infrastructure\Services;

use App\Domain\Shared\Ports\QueueServiceInterface;
use Illuminate\Support\Facades\Queue;

final class LaravelQueueService implements QueueServiceInterface
{
    public function dispatch(callable|object $job, ?string $queue = null): void
    {
        if ($queue !== null) {
            Queue::pushOn($queue, $job);
        } else {
            Queue::push($job);
        }
    }

    public function later(int $delay, callable|object $job, ?string $queue = null): void
    {
        if ($queue !== null) {
            Queue::laterOn($queue, $delay, $job);
        } else {
            Queue::later($delay, $job);
        }
    }
}
