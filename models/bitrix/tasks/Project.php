<?php

namespace app\models\bitrix\tasks;

use app\models\bitrix\traits\Collector;
use yii\base\Model;

class Project extends Model
{
    public $id;
    public $name;
    public $createdDate;

    use Collector;

    public function rules()
    {
        return [
            ['id', 'number'],
            [['name', 'createdDate'], 'string']
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'name',
            'DATE_CREATE' => 'createdDate',
        ];
    }
}