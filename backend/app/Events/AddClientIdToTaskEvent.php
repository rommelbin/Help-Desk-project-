<?php

namespace App\Events;

use App\Models\BaseModel;

class AddClientIdToTaskEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public int $id;
    public BaseModel $model;
    public function __construct($model, $attributes)
    {
        $this->model = $model;
        $this->id = auth()->id();
    }
}
