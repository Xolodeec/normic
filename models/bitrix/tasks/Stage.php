<?php

namespace app\models\bitrix\tasks;

use app\models\bitrix\traits\Collector;
use yii\base\Model;

class Stage extends Model
{
    public $id;
    public $title;
    public $color;
    public $entityId;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'entityId'], 'number'],
            [['title', 'color'], 'string']
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'TITLE' => 'title',
            'COLOR' => 'color',
            'ENTITY_ID' => 'entityId',
        ];
    }
}