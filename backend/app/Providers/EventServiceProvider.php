<?php

namespace App\Providers;

use App\Events\CreateNotificationsCommentEvent;
use App\Listeners\CreateNotificationsCommentListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\AddClientIdToTaskEvent::class => [
            \App\Listeners\ClientIdTaskListener::class,
        ],
        \App\Events\CreateFileEvent::class => [
            \App\Listeners\CreateFileListener::class
        ],
        CreateNotificationsCommentEvent::class => [
            CreateNotificationsCommentListener::class
        ]
    ];
}
