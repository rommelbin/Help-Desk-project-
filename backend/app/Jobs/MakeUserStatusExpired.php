<?php

namespace App\Jobs;

use App\Models\BaseModel;

class MakeUserStatusExpired extends Job
{
    protected BaseModel $model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->model->status === 'Ожидание') {
            $this->model->update(['status' => 'Просрочено']);
        }
    }
}
