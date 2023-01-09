<?php

namespace App\Listeners;

use App\Events\addClientIdToTaskEvent;

class ClientIdTaskListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  addClientIdToTaskEvent  $event
     * @return void
     */
    public function handle(addClientIdToTaskEvent $event)
    {
        $event->model->client_id = $event->id;
        $event->model->save();
    }
}
