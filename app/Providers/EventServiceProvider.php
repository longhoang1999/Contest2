<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\CreateNotification;
use App\Events\UpdateNotification;
use App\Events\DeleteNotification;


use App\Listeners\SendCreateNotification;
use App\Listeners\SendUpdateNotification;
use App\Listeners\SendDeleteNotification;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CreateNotification::class => [
            SendCreateNotification::class,
        ],
        UpdateNotification::class => [
            SendUpdateNotification::class,
        ],
        DeleteNotification::class => [
            SendDeleteNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}