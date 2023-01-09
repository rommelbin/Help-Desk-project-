<?php

namespace App\Models;

use App\Jobs\MakeTasksDeclinedOnClientDelete;
use App\Validators\CustomValidator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseModel extends BaseServiceLevelModel
{
    use HasFactory;

    /**
     * show One or Many Entities
     * @param int|null $id
     * @param array $attributes
     * @return array
     */
    public static function showModel(?int $id, array $attributes): array
    {
        return (is_null($id)) ? static::showMany($attributes) : static::showOne($id, $attributes);
    }

    /**
     * Show only one model from Database
     * @param int|null $id
     * @param array|null $attributes
     * @return array
     */
    public static function showOne(?int $id, ?array $attributes): array
    {
        (isset($attributes['withs'])) ? $model = static::find($id)->load($attributes['withs']) : $model = static::find($id);
        if (is_null($model))
            throw new \Exception(static::getClassName() . " doesn't exist", Response::HTTP_NOT_FOUND);
        static::askAccessToEntity('showOne', $model);
        return ['data' => $model, 'status' => Response::HTTP_OK];
    }

    /**
     * Show models with filtration
     * @param array $attributes
     * @return array
     * @throws AuthorizationException
     */
    public static function showMany(array $attributes): array
    {
        static::askAccessToModel('showMany', static::class);
        $query = static::useModifications($attributes);

        if (isset($attributes['page']) && isset($attributes['per_page'])) {
            $data = static::usePagination($query, $attributes['per_page'], $attributes['page']);
            return [
                'data' => $data,
                'pages' => $query->lastPagePagination,
                'status' => Response::HTTP_OK
            ];
        } else {
            $data = $query->get()->all();
            return [
                'data' => $data,
                'status' => Response::HTTP_OK
            ];
        }
    }

    /**
     * @throws \Exception
     */
    static function storeModel(?int $id, array $attributes): array
    {
        static::askAccessToModel('storeModel', static::class);
        $attributes = (new CustomValidator($attributes, static::$storeRules))->getAttributes();
        $model = new static($attributes);
        $model->save();
        static::useEvents('store', $model, $attributes);
        return ['data' => $model, 'status' => Response::HTTP_CREATED];
    }

    /**
     * @throws AuthorizationException
     * @throws \Exception
     */
    static function destroyModel(?int $id, array $attributes): array
    {
        $model = static::checkIDAndModel($id);
        static::askAccessToEntity('deleteModel', $model);

        $model->delete();
        return ['data' => ' ', 'status' => Response::HTTP_OK];
    }

    /**
     * @throws AuthorizationException
     * @throws \Exception
     */
    static function updateModel(?int $id, array $attributes): array
    {
        $model = static::checkIDAndModel($id);
        static::askAccessToEntity('updateModel', $model);
        $attributes = (new CustomValidator($attributes, static::$updateRules))->getAttributes();
        static::useEvents('update', $model, $attributes);
        $model->update($attributes);
        return ['data' => $model, 'status' => Response::HTTP_OK];
    }
}
