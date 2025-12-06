<?php

namespace App\Domain\Shared\Ports;

interface QueueServiceInterface
{
    public function dispatch(object $job): void;

    public function dispatchSync(object $job): void;

    public function dispatchAfterResponse(object $job): void;
}
