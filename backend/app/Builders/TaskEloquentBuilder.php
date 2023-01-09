<?php
declare(strict_types=1);

namespace App\Builders;

class TaskEloquentBuilder extends CustomEloquentBuilder
{
    public function searchMods($likeSearch): CustomEloquentBuilder
    {
        if (key($likeSearch))
            $this->where('name','ILIKE','%'.$likeSearch[key($likeSearch)].'%');

        return $this;
    }
    public function orderMods($order): CustomEloquentBuilder
    {
        foreach ($order as $value) {
            $data = explode(' ', $value);

            if ($data[0] === 'status' && $data[1] === 'sort') {
                $this->orderByRaw("CASE WHEN status = 'Новая' THEN 1 WHEN status = 'В работе' THEN 2 WHEN status = 'В ожидании' THEN 3 WHEN status = 'Выполнена' THEN 4 WHEN status = 'Закрыта' THEN 5 WHEN status = 'Отклонена' THEN 6 END");
                $this->orderBy('created_at', 'desc');
                continue;
            }
            $this->orderBy($data[0], $data[1]);
        }

        return $this;
    }
}
