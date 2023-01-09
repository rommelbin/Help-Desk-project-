<?php

namespace App\Models;

use App\Builders\TaskEloquentBuilder;
use App\Events\AddClientIdToTaskEvent;
use App\Events\CreateNotificationsCommentEvent;
use App\Validators\CustomValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class Task extends BaseModel
{
    protected
        $table = 'tasks',
        $primaryKey = 'id',
        $guarded = [],
        $casts = [
        'created_at' => 'datetime:d.m.Y H:i:s',
        'updated_at' => 'datetime:d.m.Y H:i:s',
        'completed_at' => 'datetime:d.m.Y H:i:s',
        'deadline' => 'datetime:d.m.Y H:i:s'
    ];

    protected static array
        $storeRules = [
        'name' => ['bail', 'required', 'string', 'max:100'],
        'description' => ['string', 'max:300'],
        'priority' => ['string', 'in:минимальный,средний,критический'],
        'private' => ['sometimes', 'required', 'boolean'],
        'deadline' => ['date', 'after:0 hours'],
        'executor_id' => ['integer', 'exists:users,id'],
        'status' => ['string', 'in:Новая'],
        'completed_at' => ['date', 'after:0 hours']
    ],
        $updateRules = [
        'name' => ['sometimes', 'required', 'string', 'max:100'],
        'description' => ['string', 'max:300'],
        'priority' => ['string', 'in:минимальный,средний,критический'],
        'private' => ['sometimes', 'required', 'boolean'],
        'status' => ['string', 'in:Новая,В работе,В ожидании,Выполнена,Закрыта,Отклонена'],
        'executor_id' => ['integer', 'exists:users,id'],
        'deadline' => ['date', 'after:0 hours'],
        'client_id' => ['integer', 'exists:users,id'],
        'completed_at' => ['date', 'after:0 hours']
    ],
        $events = [
        'store' => [AddClientIdToTaskEvent::class],
        'update' => [CreateNotificationsCommentEvent::class]
    ];


    /**
     * @Relation
     * */
    public function clientUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * @Relation
     */
    public function executorUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    /**
     * @Relation
     */
    public function comment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    /**
     * @Relation
     */
    public function subtask(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subtask::class, 'task_id');
    }

    static function destroyModel(?int $id, array $attributes): array
    {
        $model = static::checkIDAndModel($id);
        static::askAccessToEntity('deleteModel', $model);
        $model->update(['status' => 'Отклонена']);
        return (["data" => $model, 'status' => \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND]);
    }

    public static function updateModel(?int $id, array $attributes): array
    {
        $model = static::checkIDAndModel($id);
        $attributes = (new CustomValidator($attributes, static::$updateRules))->getAttributes();
        $model->custom_attributes = $attributes;
        static::askAccessToEntity('updateModel', $model);
        unset($model->custom_attributes);
        static::useEvents('update', $model, $attributes);
        $model->update($attributes);
        return ['data' => $model, 'status' => Response::HTTP_OK];
    }
    public function newEloquentBuilder($query): TaskEloquentBuilder
    {
        return new TaskEloquentBuilder($query);
    }
}
