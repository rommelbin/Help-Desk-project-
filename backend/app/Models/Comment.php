<?php

namespace App\Models;

use App\Builders\CommentEloquentBuilder;
use App\Events\CreateFileEvent;
use App\Validators\CustomValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class Comment extends BaseModel
{
    protected
        $table = 'comments',
        $primaryKey = 'id',
        $guarded = ['files'],
        $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i',
    ];

    protected static array
        $storeRules = [
        'task_id' => ['required', 'integer', 'exists:tasks,id'],
        'description' => ['sometimes', 'string', 'max:250'],
        'files' => ['sometimes', 'array']
    ],
        $updateRules = [
        'task_id' => ['sometimes', 'integer', 'exists:tasks,id'],
        'description' => ['sometimes', 'string', 'max:250'],
        'files' => ['sometimes', 'array']
    ],
        $events = [
        'store' => [CreateFileEvent::class],
        'update' => []
    ];

    /**
     * @Relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @Relation
     */
    public function file()
    {
        return $this->hasMany(File::class, 'comment_id');
    }

    /**
     * @Relation
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public static function showMany(array $attributes): array
    {
        if (isset($attributes['filter']['task_id']) && count(explode(',', $attributes['filter']['task_id'])) === 1) {
            $task = Task::where('id', $attributes['filter']['task_id'])->first();
            if (!(auth()->user()->id === $task->executor_id || auth()->user()->id === $task->client_id || Gate::allows('isAdmin')))
                throw new AuthorizationException('Unauthorized.', Response::HTTP_FORBIDDEN);

        }

        $query = static::useModifications($attributes);
        if (isset($attributes['page']) && isset($attributes['per_page'])) {
            $data = static::usePagination($query, $attributes['per_page'], $attributes['page']);
            foreach ($data as $key => $comment) {
                if (is_null($comment->user)) {
                    array_splice($data, $key, 1);
                    continue;
                }

                $comment->user->load('department');
                $comment->load('file');
            }
            return
                [
                    'data' => $data,
                    'pages' => $query->lastPagePagination,
                    'status' => Response::HTTP_OK
                ];
        } else {
            $data = $query->get();
            ($data->all() === []) ? $data = [] : $data = $data->all();
            return
                [
                    'data' => $data,
                    'status' => Response::HTTP_OK
                ];
        }
    }

    public
    static function storeModel(?int $id, array $attributes): array
    {
        $attributes = (new CustomValidator($attributes, static::$storeRules))->getAttributes();
        $attributes['user_id'] = auth()->id();
        $model = new static($attributes);
        static::askAccessToEntity('storeModel', $model);
        $model->save();

        static::useEvents('store', $model, $attributes);

        $model->load('file');
        return ['data' => $model, 'status' => Response::HTTP_CREATED];
    }

    public
    function newEloquentBuilder($query): CommentEloquentBuilder
    {
        return new CommentEloquentBuilder($query);
    }
}
