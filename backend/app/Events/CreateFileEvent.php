<?php

namespace App\Events;

use App\Models\BaseModel;

class CreateFileEvent extends Event
{
    public array $attributes;
    public BaseModel $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $attributes)
    {
        $this->attributes = $attributes;
        $this->model = $model;
    }
}
