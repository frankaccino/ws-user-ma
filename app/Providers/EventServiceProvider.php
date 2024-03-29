<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        'App\Events\Users\CreatedUser' => [
            'App\Listeners\Users\Logs\LogCreatedUser',
            'App\Listeners\Users\Notifications\NotifyCreatedUser'
        ],
        'App\Events\Users\DeletingUser' => [
            'App\Listeners\Users\Logs\LogDeletingUser',
            'App\Listeners\Users\Notifications\NotifyDeletingUser',
        ],
        'App\Events\Users\UpdatedUser' => [
            'App\Listeners\Users\Logs\LogUpdatedUser'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
