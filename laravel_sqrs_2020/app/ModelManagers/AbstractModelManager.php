<?php

namespace App\ModelManagers;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractModelManager
{
    abstract public function getModelName() : string;

    /**
     * универсальный метод скоупа по параметрам
     * @param array $attributes
     * @return Builder
     */
    public function byAttributes(array $attributes) : Builder
    {
        /** @var Builder $query */
        $query = $this->getModelName()::query();
        // по каждому аттрибуту добавляем условие
        foreach($attributes as $attribute => $value){
            if(is_array($value)){ // для массива in
                $query->whereIn($attribute, $value);
            } elseif(is_null($value)){ // для null тавим is null
                $query->whereNull($attribute);
            } else { // для скаляра просто е сравнение
                $query->where($attribute, $value);
            }
        }
        return $query;
    }
}
