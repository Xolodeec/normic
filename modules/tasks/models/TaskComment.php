<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use yii\base\Model;
use function Symfony\Component\String\u;

class TaskComment extends Model
{
    public $id;
    public $postMessageHtml;
    public $authorId;
    public $authorName;
    public $authorEmail;
    public $date;
    public $message;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'authorId'], 'number'],
            [['postMessageHtml', 'authorName', 'authorEmail', 'date', 'message'], 'string'],
            ['message', 'filter', 'filter' => function($item){
                return u($item)->before('#От')->toString();
            }],
        ];
    }

    public static function mapFields()
    {
        return [
            'POST_MESSAGE_HTML' => 'postMessageHtml',
            'ID' => 'id',
            'AUTHOR_ID' => 'authorId',
            'AUTHOR_NAME' => 'authorName',
            'AUTHOR_EMAIL' => 'authorEmail',
            'POST_DATE' => 'date',
            'POST_MESSAGE' => 'message',
        ];
    }

    public static function getList(int $taskId)
    {
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('task.commentitem.getlist', ['taskId' => $taskId, 'ORDER' => ['ID' => 'desc']]);

        if(!empty($result))
        {
            return static::multipleCollect(new static(), $result);
        }

        return [];
    }
}
