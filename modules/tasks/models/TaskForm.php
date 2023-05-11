<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use Tightenco\Collect\Support\Collection;
use yii\base\BaseObject;
use yii\base\Model;

class TaskForm extends Model
{
    public $id;
    public $title;
    public $description;
    public $deadline;
    public $groupId;
    public $responsibleId;
    public $createdBy;

    private $companyId;
    private $contactId;

    public function rules()
    {
        return [
            [['id', 'groupId'], 'number'],
            [['title', 'description'], 'string'],
            [['deadline'], 'safe'],
            ['responsibleId' , 'default', 'value' => \Yii::$app->params['createTask']['Ответственный']],
            ['createdBy' , 'default', 'value' => \Yii::$app->params['createTask']['Постановщик']],
            [['title', 'groupId'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название задачи',
            'deadline' => 'Крайний срок',
            'groupId' => 'Проект',
        ];
    }

    public function setCompany($companyId)
    {
        $this->companyId = $companyId;
    }

    public function setContact($contactId)
    {
        $this->contactId = $contactId;
    }

    public function getListProject()
    {
        $bitrix = new Bitrix();
        $projects = new Collection();

        $commands['get_company'] = $bitrix->buildCommand('crm.company.get', ['ID' => $this->companyId]);
        $commands['get_project_company'] = $bitrix->buildCommand('sonet_group.get', ['FILTER' => ['%NAME' => '$result[get_company][TITLE]']]);

        ['result' => ['result' => $response]] = $bitrix->batchRequest($commands);

        if(!empty($response['get_project_company']))
        {
            $projects = collect(Project::multipleCollect(new Project(), $response['get_project_company']));
        }

        return $projects->toArray();
    }

    public function create()
    {
        $task = new Task();
        $task->setAttributes($this->getAttributes());
        $task->ufCrmTask = ["CO_{$this->companyId}", "C_{$this->contactId}"];

        return $task->create()['result']['task']['id'];
    }

    public function setRelationTask($task)
    {
        $this->id = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->groupId = $task->groupId;
        $this->deadline = $task->deadline;
    }

    public function update()
    {
        $task = new Task();
        $task->setAttributes($this->getAttributes());
        $task->ufCrmTask = ["CO_{$this->companyId}", "C_{$this->contactId}"];

        return $task->update();
    }
}
