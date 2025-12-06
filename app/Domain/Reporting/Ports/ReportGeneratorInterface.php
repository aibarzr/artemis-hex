<?php

namespace App\Domain\Reporting\Ports;

interface ReportGeneratorInterface
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $options
     */
    public function generate(array $data, array $options = []): string;

    public function getFormat(): string;
}
