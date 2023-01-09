<?php
declare(strict_types=1);

namespace App\Builders;

use App\Exceptions\FilterException;

class CommentEloquentBuilder extends CustomEloquentBuilder
{
    public function filterMods(array $filter): CustomEloquentBuilder
    {
        if (is_numeric(key($filter)))
            throw new FilterException('Comments. Такой тип фильтрации не поддерживается. Фильтруйте правильно: filter[key]=value', 422);
        $this->where(key($filter), '=', $filter[key($filter)]);

        return $this;
    }
}
