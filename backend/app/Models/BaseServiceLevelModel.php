<?php

namespace App\Models;

use App\Builders\CustomEloquentBuilder;
use Carbon\Carbon as Carbon;
use Firebase\JWT\JWT;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;

class BaseServiceLevelModel extends Model
{
    /**
     * @var array|string[]
     */
    protected static array
        $events,
        $modificationsArray =
        [
            'withs',
            'select',
            'filter',
            'order',
            'search'
        ];

    public static function getClassName(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    protected static function useModifications(array $attributes)
    {
        $query = static::query();
        $modifications = self::$modificationsArray;
        foreach (array_keys($attributes) as $array_key) {
            if (in_array($array_key, $modifications)) {
                $mods = $array_key . 'Mods';
                $query->$mods($attributes[$array_key]);
            }
        }
        return $query;
    }

    protected static function usePagination($query, int $page, int $per_page)
    {
        $items = $query->paginate($page, '*', '', $per_page);
        $query->lastPagePagination = $items->lastPage();
        return $items->all();
    }

    protected static function useEvents($methodName, BaseModel $model, array $attributes)
    {
        if (in_array($methodName, array_keys(static::$events))) {
            $events = static::$events[$methodName];
        }
        foreach ($events as $event) {
            event(new $event($model, $attributes));
        }
    }

    public function newEloquentBuilder($query): CustomEloquentBuilder
    {
        return new CustomEloquentBuilder($query);
    }

    public static function checkIDAndModel(?int $id)
    {
        if (!isset($id))
            throw new \Exception('ID is not found', Response::HTTP_NOT_FOUND);

        if (!($model = static::findOrFail($id)))
            throw new \Exception('Model not found', Response::HTTP_NOT_FOUND);
        return $model;
    }

    public static function encodeToken(?int $id): string
    {
        $payload = [
            'iss' => "help-desk", // Issuer of the token
            'sub' => $id, // Subject of the token
            'exp' => Carbon::now()->addDays(1)->timestamp,
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * @throws AuthorizationException
     */
    public static function askAccessToEntity(string $method, ?Model $model)
    {
        if (!auth()->user()->can($method, $model))
            throw new AuthorizationException('Unauthorized.', Response::HTTP_FORBIDDEN);
    }

    public static function askAccessToModel(string $method, ?string $model)
    {
        if (!auth()->user()->can($method, $model))
            throw new AuthorizationException('Unauthorized.', Response::HTTP_FORBIDDEN);
    }
}
