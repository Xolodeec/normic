<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use app\models\User;
use yii\base\Model;

class CommentForm extends Model
{
    public $taskId;
    public $id;
    public $message;

    public function rules()
    {
        return [
            ['message', 'required', 'when' => function($model){
                return empty($this->id);
            }],
            ['message', 'string'],
            ['message', 'filter', 'filter' => function($item){
                return trim($item);
            }],
            [['taskId', 'id'], 'number']
        ];
    }

    public function setTaskId(int $taskId)
    {
        $this->taskId = $taskId;
    }

    public function sendCommentByUser(User $user)
    {
        $bitrix = new Bitrix();

        return $bitrix->request('task.commentitem.add', ['taskId' => $this->taskId, 'fields' => [
            'AUTHOR_ID' => 55,
            'POST_MESSAGE' => "{$this->message}\n\n #От {$user->lastName} {$user->name}",
        ]]);
    }

    public function update()
    {
        $bitrix = new Bitrix();

        return $bitrix->request('task.commentitem.update', ['taskId' => $this->taskId, 'ITEMID' => $this->id, 'FIELDS' => ['POST_MESSAGE' => $this->message]]);
    }

    public function deleteComment()
    {
        $bitrix = new Bitrix();

        return $bitrix->request('task.commentitem.delete', ['taskId' => $this->taskId, 'ITEMID' => $this->id]);
    }
}
