<?php

namespace App\Providers;

use App\Domain\Attendance\Repositories\AnomalyRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceLogRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use App\Domain\Attendance\Repositories\PersonRepositoryInterface;
use App\Infrastructure\Attendance\Persistence\EloquentAnomalyRepository;
use App\Infrastructure\Attendance\Persistence\EloquentAttendanceLogRepository;
use App\Infrastructure\Attendance\Persistence\EloquentAttendanceRecordRepository;
use App\Infrastructure\Attendance\Persistence\EloquentContextEventRepository;
use App\Infrastructure\Attendance\Persistence\EloquentDailySummaryRepository;
use App\Infrastructure\Attendance\Persistence\EloquentLaborLinkRepository;
use App\Infrastructure\Attendance\Persistence\EloquentLicenseRepository;
use App\Infrastructure\Attendance\Persistence\EloquentPersonRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AttendanceRecordRepositoryInterface::class, EloquentAttendanceRecordRepository::class);
        $this->app->bind(DailySummaryRepositoryInterface::class, EloquentDailySummaryRepository::class);
        $this->app->bind(LicenseRepositoryInterface::class, EloquentLicenseRepository::class);
        $this->app->bind(ContextEventRepositoryInterface::class, EloquentContextEventRepository::class);
        $this->app->bind(AnomalyRepositoryInterface::class, EloquentAnomalyRepository::class);
        $this->app->bind(
            ContextEventRepositoryInterface::class,
            EloquentContextEventRepository::class
        );
        $this->app->bind(AttendanceLogRepositoryInterface::class, EloquentAttendanceLogRepository::class);
        $this->app->bind(PersonRepositoryInterface::class, EloquentPersonRepository::class);
        $this->app->bind(LaborLinkRepositoryInterface::class, EloquentLaborLinkRepository::class);
        $this->app->bind(
            \App\Domain\Attendance\Repositories\MonthlySummaryRepositoryInterface::class,
            \App\Infrastructure\Attendance\Persistence\EloquentMonthlySummaryRepository::class
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
