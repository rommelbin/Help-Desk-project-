<?php

namespace App\Models;

class Status extends BaseModel
{
    protected
        $table = 'statuses',
        $primaryKey = 'id',
        $guarded = [];

    public $timestamps = false;

    protected static array
        $storeRules = [
            'status' => ['bail', 'required', 'string', 'unique:statuses,status'],
        ],
        $updateRules = [
            'status' => ['bail', 'required', 'string', 'unique:statuses,status'],
        ],
        $events = [
        'store' => [],
        'update' => []
        ];


    /**
     * @Relation
     */
    public function task()
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
