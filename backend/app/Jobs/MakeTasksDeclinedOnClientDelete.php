<?php

namespace App\Jobs;

use App\Models\Task;

class MakeTasksDeclinedOnClientDelete extends Job
{
    protected int $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Task::where('client_id', $this->id)->update(['status' => 'Отклонена']);
    }
}
