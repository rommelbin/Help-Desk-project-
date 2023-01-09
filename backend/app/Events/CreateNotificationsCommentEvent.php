<?php

namespace App\Events;

use App\Models\BaseModel;

class CreateNotificationsCommentEvent extends Event
{
    public BaseModel $model;
    public array $attributes;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $attributes)
    {
        $this->model = $model;
        $this->attributes = $attributes;
    }
}
