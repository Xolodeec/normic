<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\tasks\Stage;
use app\models\bitrix\User;
use Tightenco\Collect\Support\Collection;
use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class FilterForm extends Model
{
    public $tasks;
    public $page;
    public $amountTask;
    public $projectId;
    public $statusId;
    public $_projects;
    public $companyId;
    public $createDate;

    public function rules()
    {
        return [
            [['page', 'amountTask', 'projectId', 'companyId', 'statusId'], 'number'],
            [['amountTask', 'page'], 'default', 'value' => 0],
            [['tasks'], 'default', 'value' => []],
            [['createDate'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'projectId' => 'Проект',
            'statusId' => 'Статус',
            'createDate' => 'Дата создания',
        ];
    }

    public function filter()
    {
        $bitrix = new Bitrix();

        $params = [
            'order' => ['ID' => 'DESC'],
            'filter' => [
                'UF_CRM_TASK' => 'CO_' . $this->companyId,
            ],
            'select' => collect(Task::mapFields())->keys()->toArray(),
            'start' => $this->page * 50,
        ];

        if(!empty($this->projectId))
        {
            $params['filter']['=GROUP_ID'] = $this->projectId;
        }

        if(!empty($this->statusId))
        {
            $params['filter']['=STATUS'] = $this->statusId;
        }

        if(!empty($this->createDate))
        {
            $params['filter']['>=CREATED_DATE'] = date('Y-m-d 00:00', strtotime($this->createDate));
            $params['filter']['<CREATED_DATE'] = date('Y-m-d 23:59', strtotime($this->createDate));
        }

        $commands['get_tasks'] = $bitrix->buildCommand('tasks.task.list', $params);

        $commands['get_company'] = $bitrix->buildCommand('crm.company.get', ['ID' => $this->companyId]);
        $commands['get_project_company'] = $bitrix->buildCommand('sonet_group.get', ['FILTER' => ['%NAME' => '$result[get_company][TITLE]']]);

        $paramsGetResponsible = [];

        for($i = 0; $i < 50; $i++)
        {
            $paramsGetResponsible[] = '$result[get_tasks][tasks][' . $i . '][responsibleId]';
        }

        $commands['get_responsible'] = $bitrix->buildCommand('user.get', ['ID' => $paramsGetResponsible]);

        ['result' => $response] = $bitrix->batchRequest($commands);

        $this->_projects = Project::multipleCollect(Project::class, $response['result']['get_project_company']);

        if($this->validate() && !empty($response['result']['get_tasks']['tasks']))
        {
            $this->tasks = Task::multipleLoad($response['result']['get_tasks']['tasks']);
            $responsible = User::multipleCollect(new User(), $response['result']['get_responsible']);

            if(!empty($this->tasks))
            {
                $tasks = new Collection();

                foreach ($this->tasks as $index => $task)
                {
                    $responsibleIndex = collect($responsible)->search(function ($item) use($task) {
                        return $item->id == $task->responsibleId;
                    });

                    $task->responsible = $responsible[$responsibleIndex];

                    if(!empty($this->_projects))
                    {
                        $taskProjectIndex = collect($this->_projects)->search(function ($item) use($task) {
                            return $item->id == $task->groupId;
                        });
                        
                        $task->_project = $this->_projects[$taskProjectIndex];
                    }

                    $tasks->put($index + ($this->page * 50) + 1, $task);
                }

                $this->tasks = $tasks->toArray();
            }
        }

        $this->amountTask = $response['result_total']['get_tasks'];
    }

    public static function generate($companyId = null)
    {
        $model = new static();
        $bitrix = new Bitrix();

        $commands['get_tasks'] = $bitrix->buildCommand('tasks.task.list', [
            'order' => ['ID' => 'DESC'],
            'filter' => [
                'UF_CRM_TASK' => 'CO_' . $companyId,
            ],
            'select' => collect(Task::mapFields())->keys()->toArray(),
            'start' => $model->page * 50,
        ]);

        $commands['get_company'] = $bitrix->buildCommand('crm.company.get', ['ID' => $companyId]);
        $commands['get_project_company'] = $bitrix->buildCommand('sonet_group.get', ['FILTER' => ['%NAME' => '$result[get_company][TITLE]']]);

        $paramsGetResponsible = [];

        for($i = 0; $i < 50; $i++)
        {
            $paramsGetResponsible[] = '$result[get_tasks][tasks][' . $i . '][responsibleId]';
        }

        $commands['get_responsible'] = $bitrix->buildCommand('user.get', ['ID' => $paramsGetResponsible]);

        ['result' => $response] = $bitrix->batchRequest($commands);

        if($model->validate() && !empty($response['result']['get_tasks']['tasks']))
        {
            $model->tasks = Task::multipleLoad($response['result']['get_tasks']['tasks']);
            $model->_projects = Project::multipleCollect(Project::class, $response['result']['get_project_company']);
            $responsible = User::multipleCollect(new User(), $response['result']['get_responsible']);

            if(!empty($model->tasks))
            {
                $tasks = new Collection();

                foreach ($model->tasks as $index => $task)
                {
                    $responsibleIndex = collect($responsible)->search(function ($item) use($task) {
                        return $item->id == $task->responsibleId;
                    });

                    $taskProjectIndex = collect($model->_projects)->search(function ($item) use($task) {
                        return $item->id == $task->groupId;
                    });

                    $task->responsible = $responsible[$responsibleIndex];
                    $task->_project = $model->_projects[$taskProjectIndex];

                    $tasks->put($index + ($model->page * 50) + 1, $task);
                }

                $model->tasks = $tasks->toArray();
            }
        }

        $model->amountTask = $response['result_total']['get_tasks'];

        return $model;
    }

    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }
}