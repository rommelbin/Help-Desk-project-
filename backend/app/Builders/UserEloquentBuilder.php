<?php

namespace App\Builders;

class UserEloquentBuilder extends CustomEloquentBuilder
{
    public function orderMods($order): CustomEloquentBuilder
    {
        foreach ($order as $value) {
            $data = explode(' ', $value);

            if ($data[0] === 'role' && $data[1] === 'sort') {
                $this->orderByRaw("CASE WHEN role = 'Администратор' THEN 1 WHEN role = 'Исполнитель' THEN 2 WHEN role = 'Заказчик' THEN 3 END");
                $this->orderBy('id', 'asc');
                continue;
            }
            $this->orderBy($data[0], $data[1]);
        }

        return $this;
    }

}
