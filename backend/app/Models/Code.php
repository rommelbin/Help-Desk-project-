<?php

namespace App\Models;

use App\Exceptions\CodeException;
use App\Jobs\SendResetPasswordCode;
use App\Validators\CustomValidator;
use Carbon\Carbon as Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class Code extends BaseModel
{
    protected
        $table = 'codes',
        $primaryKey = 'id',
        $guarded = [],
        $hidden = ['name'],
        $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i'
    ];

    protected static array
        $storeRules = [
        'name' => ['bail', 'required', 'string'],
        'user_id' => ['integer', 'exists:users,id'],
        'counter' => ['required', 'integer', 'max:5']
    ],
        $updateRules = [
        'name' => ['sometimes', 'required', 'string'],
        'counter' => ['sometimes', 'required', 'integer', 'max:5']
    ],
        $sendRules = [
        'email' => ['required', 'exists:users,email'],// 'regex:/^(?!.* )[0-9A-Za-z\x20-\x26\x3A-\x40\x5B-\x5F\x7B-\x7E]+@smartworld\.team$/', 'between:17,100'],
    ],
        $checkRules = [
        'name' => ['required', 'string', 'size:10']
    ],
        $events = [
        'store' => [],
        'update' => []
    ];

    /**
     * @Relation
     * */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function sendModel(?int $id, array $attributes): array
    {
        $attributes = (new CustomValidator($attributes, static::$sendRules))->getAttributes();
        $model = User::where('email', $attributes['email'])->first();
        if ($model->status != 'Активен') {
            throw new CodeException('Статус должен быть Активен', Response::HTTP_FORBIDDEN);
        }
        $attributes['id'] = $model->id;
        static::where('user_id', $attributes['id'])->delete();
        dispatch(new SendResetPasswordCode($attributes));

        return ['data' => ['user_id' => $attributes['id']], 'status' => Response::HTTP_OK];
    }

    public static function checkModel(?int $id, array $attributes): array
    {
        $attributes = (new CustomValidator($attributes, static::$checkRules))->getAttributes();
        $model = static::where('user_id', $id)->first();
        if (is_null($model))
            throw new CodeException('Для данного пользователя кода восстановления нет', 404);

        if ($model->counter >= 5) {
            $model->delete();
            throw new CodeException('Превышено число попыток', 410);
        } else {
            if ($model->created_at->diff(Carbon::now()->toDateTime())->format('%I') >= 30) {
                $model->delete();
                throw new CodeException('Время жизни кода истекло', 403);
            }
            if (Hash::check($attributes['name'], $model->name)) {
                $model->delete();
                return ['data' => ['Authorization' => static::encodeToken($id)], 'status' => Response::HTTP_OK];
            }
        }
        $model->counter += 1;
        $model->save();
        throw new CodeException('Введен неверный код', 404);
    }
}
