<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\tasks\Stage;
use app\models\bitrix\User;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class FilterForm extends Model
{
    public $tasks;
    public $page;
    public $amountTask;

    public function rules()
    {
        return [
            [['page', 'amountTask'], 'number'],
            [['amountTask', 'page'], 'default', 'value' => 0],
            [['tasks'], 'default', 'value' => []],
        ];
    }

    public static function generate($companyId = null)
    {
        $model = new static();
        $bitrix = new Bitrix();

        $commands['get_tasks'] = $bitrix->buildCommand('tasks.task.list', [
            'order' => ['ID' => 'DESC'],
            'filter' => [['UF_CRM_TASK' => ['CO_' . $companyId]]],
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
            $projects = Project::multipleCollect(Project::class, $response['result']['get_project_company']);
            $responsible = User::multipleCollect(new User(), $response['result']['get_responsible']);

            if(!empty($model->tasks))
            {
                $tasks = new Collection();

                foreach ($model->tasks as $index => $task)
                {
                    $responsibleIndex = collect($responsible)->search(function ($item) use($task) {
                        return $item->id == $task->responsibleId;
                    });

                    $taskProjectIndex = collect($projects)->search(function ($item) use($task) {
                        return $item->id == $task->groupId;
                    });

                    $task->responsible = $responsible[$responsibleIndex];
                    $task->_project = $projects[$taskProjectIndex];

                    $tasks->put($index + ($model->page * 50) + 1, $task);
                }

                $model->tasks = $tasks->toArray();
            }
        }

        $model->amountTask = $response['result_total']['get_tasks'];

        return $model;
    }
}