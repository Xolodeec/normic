<?php

namespace app\models\bitrix;

use app\models\bitrix\traits\Collector;
use yii\base\Model;

class User extends Model
{
    public $id;
    public $name;
    public $lastName;
    public $secondName;

    use Collector;

    public function rules()
    {
        return [
            ['id', 'number'],
            [['name', 'lastName', 'secondName'], 'string'],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'name',
            'LAST_NAME' => 'lastName',
            'SECOND_NAME' => 'secondName'
        ];
    }
}
