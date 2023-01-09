<?php
declare(strict_types=1);
namespace App\Builders;

use Illuminate\Database\Eloquent\Builder as DefaultBuilder;

class CustomEloquentBuilder extends DefaultBuilder
{
    public ?int $lastPagePagination;
    /**
     * @param array $filter
     * @return $this
     * @throws \Exception
     */
    public function filterMods(array $filter): CustomEloquentBuilder
    {

        foreach ($filter as $key => $value) {

            if($value == '')
                continue;

            if (!is_numeric($key)) {
                if (strpos($value, ',')) {
                    $this->whereIn($key, explode(',', $value));
                } else {
                    $this->where($key, '=', $value);
                }
                continue;
            }
            $data = explode('.', $value);
            $this->where($data[0], $data[1], $data[2]);
        }
        return $this;
    }

    /**
     * @param $order
     * @return $this
     * @throws \Exception
     */
    public function orderMods($order): CustomEloquentBuilder
    {
        foreach ($order as $value) {
            $data = explode(' ', $value);
            $this->orderBy($data[0], $data[1]);
        }
        return $this;
    }

    /**
     * @param $select
     * @return CustomEloquentBuilder
     */
    public function selectMods($select): CustomEloquentBuilder
    {
        return $this->select($select);
    }

    /**
     * @param array $withs
     * @return DefaultBuilder
     */
    public function withsMods(array $withs): DefaultBuilder
    {
        return $this->with($withs);
    }

}
