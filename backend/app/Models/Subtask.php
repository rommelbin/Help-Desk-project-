<?php

namespace App\Models;

use App\Exceptions\ValidationException;
use App\Validators\CustomValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class Subtask extends BaseModel
{
    protected
        $table = 'subtasks',
        $primaryKey = 'id',
        $guarded = [],
        $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i',
        'completed_at' => 'datetime:d.m.Y H:i:s',
        'deadline' => 'datetime:d.m.Y H:i:s'
    ];

    protected static array
        $storeRules = [
        'name' => ['bail', 'required', 'string', 'max:100'],
        'is_ready' => ['required', 'boolean'],
        'executor_id' => ['integer', 'exists:users,id'],
        'task_id' => ['required', 'integer', 'exists:tasks,id'],
        'completed_at' => ['sometimes', 'required', 'date'],
        'deadline' => ['date', 'after:0 hours'],
    ],
        $updateRules = [
        'name' => ['sometimes', 'required', 'string', 'max:100'],
        'is_ready' => ['sometimes', 'required', 'boolean'],
        'executor_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
        'task_id' => ['sometimes', 'required', 'integer', 'exists:tasks,id'],
        'completed_at' => ['sometimes', 'required', 'date'],
        'deadline' => ['date', 'after:0 hours'],
    ],
        $events = [
        'store' => [],
        'update' => []
    ];

    /**
     * @Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    /**
     * @Relation
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public static function storeModel(?int $id, array $attributes): array
    {
        $attributes = (new CustomValidator($attributes, static::$storeRules))->getAttributes();
        $model = new static($attributes);
        static::askAccessToEntity('storeModel', $model);
        $model->save();
        static::useEvents('store', $model, $attributes);
        return ['data' => $model, 'status' => Response::HTTP_CREATED];
    }
}
