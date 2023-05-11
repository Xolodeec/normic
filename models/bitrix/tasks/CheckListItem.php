<?php

namespace app\models\bitrix\tasks;

use app\models\bitrix\traits\Collector;
use yii\base\Model;

class CheckListItem extends Model
{
    public $id;
    public $isComplete;
    public $title;
    public $parentId;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'parentId'], 'number'],
            [['isComplete', 'title'], 'string']
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'IS_COMPLETE' => 'isComplete',
            'TITLE' => 'title',
            'PARENT_ID' => 'parentId',
        ];
    }

    public function isCompleted()
    {
        return $this->isComplete === 'Y';
    }
}
