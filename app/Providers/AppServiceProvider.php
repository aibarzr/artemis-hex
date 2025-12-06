<?php

namespace App\Providers;

use App\Domain\Application\Ports\ApplicationRepositoryInterface;
use App\Domain\Evaluator\Ports\EvaluatorRepositoryInterface;
use App\Domain\Reporting\Ports\NotificationServiceInterface;
use App\Domain\Reporting\Ports\ReportGeneratorInterface;
use App\Domain\Shared\Ports\CacheServiceInterface;
use App\Domain\Shared\Ports\QueueServiceInterface;
use App\Domain\Shared\Ports\TransactionServiceInterface;
use App\Domain\Validation\Ports\ValidationServiceInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentApplicationRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentEvaluatorRepository;
use App\Infrastructure\Services\ExcelReportGenerator;
use App\Infrastructure\Services\LaravelCacheService;
use App\Infrastructure\Services\LaravelNotificationService;
use App\Infrastructure\Services\LaravelQueueService;
use App\Infrastructure\Services\LaravelTransactionService;
use App\Infrastructure\Validation\ApplicationValidationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ApplicationRepositoryInterface::class,
            EloquentApplicationRepository::class
        );

        $this->app->bind(
            EvaluatorRepositoryInterface::class,
            EloquentEvaluatorRepository::class
        );

        $this->app->singleton(
            ValidationServiceInterface::class,
            ApplicationValidationService::class
        );

        $this->app->singleton(
            CacheServiceInterface::class,
            LaravelCacheService::class
        );

        $this->app->singleton(
            QueueServiceInterface::class,
            LaravelQueueService::class
        );

        $this->app->singleton(
            NotificationServiceInterface::class,
            LaravelNotificationService::class
        );

        $this->app->singleton(
            TransactionServiceInterface::class,
            LaravelTransactionService::class
        );

        $this->app->bind(
            ReportGeneratorInterface::class,
            ExcelReportGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
