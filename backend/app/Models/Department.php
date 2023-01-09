<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\Response;

class Department extends BaseModel
{
    protected
        $table = 'departments',
        $primaryKey = 'id',
        $guarded = [];

    protected static array
        $storeRules = [
        'name' => ['required', 'string', 'between:2,50', 'unique:departments,name']
    ],
        $updateRules = [
        'name' => ['required', 'string', 'between:2,50', 'unique:departments,name']
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
        return $this->hasMany(User::class, 'department_id');
    }

    public static function destroyModel(?int $id, array $attributes): array
    {
        $department = Department::where('name', '=', 'Без отдела')->first();
        if ($department->id === $id)
            return ['data' => ['Удаление отдела <<' . $department->name . '>> невозможно'], 'status' => RESPONSE::HTTP_FORBIDDEN];
        return parent::destroyModel($id, $attributes);
    }

}
