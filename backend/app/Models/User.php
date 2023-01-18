<?php

namespace App\Models;

use App\Builders\UserEloquentBuilder;
use App\Exceptions\CodeException;
use App\Exceptions\ValidationException;
use App\Jobs\MakeTasksDeclinedOnClientDelete;
use App\Jobs\MakeUserStatusExpired;
use App\Validators\CustomValidator;
use Carbon\Carbon;
use Doctrine\DBAL\Driver\PDO\Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Auth\Authorizable;
use App\Jobs\SendRegistrationMail;
use Symfony\Component\HttpFoundation\Response;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    protected
        $table = 'users',
        $primaryKey = 'id',
        $guarded = ['created_at', 'updated_at'],
        $hidden = ['password'],
        $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i',],
        $dates = ['deleted_at'];
    protected static array
        $storeRules = [
        'name' => ['sometimes', 'required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'surname' => ['sometimes', 'required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'email' => ['bail', 'sometimes', 'unique:users,email', 'required', 'regex:/^(?!.* )[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x3F\x5B-\x5F\x7B-\x7E]+@smartworld\.team$/', 'max:100'],
        'password' => ['sometimes', 'required', 'regex:/^(?!.* )(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x40\x5B-\x5F\x7B-\x7E]{8,30}$/'],
        'department_id' => ['integer', 'exists:departments,id'],
        'role' => ['string', 'in:Заказчик,Администратор,Исполнитель'],
        'status' => ['string', 'in:Ожидание,Активен,Просрочено']
    ],
        $updateRules = [
        'name' => ['sometimes', 'required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'surname' => ['sometimes', 'required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'password' => ['sometimes', 'required', 'regex:/^(?!.* )(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x40\x5B-\x5F\x7B-\x7E]{8,30}$/'],
        'department_id' => ['integer', 'exists:departments,id'],
        'role' => ['string', 'in:Заказчик,Администратор,Исполнитель'],
        'status' => ['string', 'in:Ожидание,Активен,Просрочено']
    ],
        $loginRules = [
        'password' => ['required', 'regex:/^(?!.* )(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x40\x5B-\x5F\x7B-\x7E]{8,30}$/'],
        'email' => ['required', 'exists:users,email', 'regex:/^(?!.* )[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x3F\x5B-\x5F\x7B-\x7E]+@smartworld\.team$/', 'max:100'],
    ],
        $confirmRegisterRules = [
        'name' => ['required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'surname' => ['required', 'regex:/^(?=.*[А-Я])(?!.* )[А-ЯЁа-яё\-\']{1,40}$/u'],
        'password' => ['required', 'regex:/^(?!.* )(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x40\x5B-\x5F\x7B-\x7E]{8,30}$/'],
        'department_id' => ['integer', 'exists:departments,id']
    ],
        $updatePasswordRules = [
        'password' => ['sometimes', 'required', 'regex:/^(?!.* )(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\dA-Za-z\x20-\x26\x28-\x2F\x3A-\x40\x5B-\x5F\x7B-\x7E]{8,30}$/'],
    ],
        $events = [
        'store' => [],
        'update' => []
    ];


    /**
     * @Relation
     */
    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @Relation
     */
    public function comment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * @Relation
     */
    public function clientTask(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'client_id');
    }

    /**
     * @Relation
     */
    public function executorTask(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'executor_id');
    }

    /**
     * @Relation
     */
    public function subtask(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subtask::class, 'executor_id');
    }

    /**
     * @Relation
     */
    public function code(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Code::class, 'user_id');
    }

    public static function storeModel(?int $id, array $attributes): array
    {
        static::askAccessToModel('storeModel', static::class);
        $attributes = (new CustomValidator($attributes, static::$storeRules))->getAttributes();
        $model = new static($attributes);
        $model->save();
        dispatch(new SendRegistrationMail($model, $attributes));
        Queue::later(Carbon::now()->addDays(1), new MakeUserStatusExpired($model));
        return ['data' => $model, 'status' => Response::HTTP_CREATED];
    }

    public static function loginModel(?int $id, array $attributes): array
    {
        $attributes = (new CustomValidator($attributes, static::$loginRules))->getAttributes();
        $model = static::where('email', $attributes['email'])->first();


        if (Hash::check($attributes['password'] ?: null, $model->password)) {

            return ['data' => [
                'Authorization' => static::encodeToken($model->id),
                'id' => $model->id,
                'role' => $model->role,
                'name' => $model->name,
                'surname' => $model->surname
            ], 'status' => Response::HTTP_OK];
        } else {
            throw new CodeException('Incorrect Password', 404);
        }
    }

    public static function updateModel(?int $id, array $attributes): array
    {
        $model = static::checkIDAndModel($id);
        static::askAccessToModel('updateModel', static::class);
        $attributes = (new CustomValidator($attributes, static::$updateRules))->getAttributes();
        $model->update($attributes);
        return ['data' => $model, 'status' => Response::HTTP_OK];
    }

    public static function sendMailModel(?int $id, array $attributes): array
    {
        static::askAccessToModel('sendMailModel', static::class);
        return (is_null($id)) ? static::sendManyMailModel($attributes) : static::sendOneMailModel($id);
    }

    public static function sendOneMailModel(int $id): array
    {
        $model = static::checkIDAndModel($id);
        if ($model->status != 'Просрочено') {
            return ['data' => 'Status should be Просрочено', 'status' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
        $attributes = ['email' => $model->email];
        dispatch(new SendRegistrationMail($model, $attributes));
        $model->status = 'Ожидание';
        $model->save();
        return ['data' => ' ', 'status' => Response::HTTP_OK];
    }

    public static function sendManyMailModel(array $attributes): array
    {
        foreach ($attributes as $id) {
            static::sendOneMailModel($id);
        }
        return ['data' => ' ', 'status' => Response::HTTP_OK];
    }

    public static function updatePasswordModel(?int $id, array $attributes): array
    {
        if (auth()->id() != $id)
            throw new AuthorizationException('Unauthorized.', Response::HTTP_FORBIDDEN);
        $model = static::checkIDAndModel($id);
        $attributes = (new CustomValidator($attributes, static::$updatePasswordRules))->getAttributes();
        $attributes['password'] = Hash::make($attributes['password']);

        $model->update($attributes);
        return ['data' => $model, 'status' => Response::HTTP_OK];
    }

    public static function confirmRegisterModel(?int $id, array $attributes): array
    {
        if (auth()->id() != $id)
            throw new AuthorizationException('Unauthorized.', Response::HTTP_FORBIDDEN);
        $model = static::checkIDAndModel($id);

        if ($model->status != 'Ожидание')
            throw new \Exception("Статус должен быть Ожидание", Response::HTTP_METHOD_NOT_ALLOWED);

        $attributes = (new CustomValidator($attributes, static::$confirmRegisterRules))->getAttributes();
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['status'] = 'Активен';

        $model->update($attributes);

        return [
            'data' => [
                'Authorization' => static::encodeToken($model->id),
                'id' => $model->id,
                'role' => $model->role,
                'email' => $model->email
            ],
            'status' => Response::HTTP_OK];
    }


    public static function destroyModel(?int $id, array $attributes): array
    {
        $user_to_delete = User::find($id);
        if (is_null($user_to_delete))
            throw new \Exception('User not found', 404);

        if ($user_to_delete->role === 'Исполнитель' && Task::where('executor_id', '=', $user_to_delete->id)->where('status', '<>', 'Закрыта')->where('status', '<>', 'Отклонена')->count() !== 0)
            return ['data' => 'Нельзя удалить исполнителя, который ответственен за выполнение задачи', 'status' => Response::HTTP_OK];
        static::askAccessToEntity('deleteModel', $user_to_delete);
        dispatch(new MakeTasksDeclinedOnClientDelete($id));
            return parent::destroyModel($id, $attributes);
    }

    public function newEloquentBuilder($query): UserEloquentBuilder
    {
        return new UserEloquentBuilder($query);
    }
}
