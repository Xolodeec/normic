<?php

namespace app\modules\profile\models;

class Product extends \app\models\bitrix\crm\Product
{
    public $companyId;

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules = $rules->push(['companyId', 'default', 'value' => []]);
        $rules = $rules->push(['companyId', 'filter', 'filter' => function($item){
            return collect($item)->has('value') ? $item['value'] : [];
        }]);
        $rules = $rules->push(['catalogId', 'default', 'value' => 14]);
        $rules = $rules->push(['sectionId', 'default', 'value' => 13]);
        $rules = $rules->push(['currencyId', 'default', 'value' => 'RUB']);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('PROPERTY_64', 'companyId');

        return $mapFields->toArray();
    }

    public static function findByCompany($companyId)
    {
        return static::getList(['PROPERTY_64' => $companyId]);
    }
}
