<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\tasks\CheckListItem;
use app\models\bitrix\tasks\Stage;
use app\models\bitrix\User;
use yii\base\BaseObject;
use function Symfony\Component\String\u;

class Task extends \app\models\bitrix\tasks\Task
{
    public $responsible;
    public $_stages;
    public $_project;
    public $_checklist;

    public function getNameProject()
    {
        return $this->_project->name;
    }

    public function getStage()
    {
        $stageId = $this->stageId;

        $stageIndex = collect($this->_stages)->search(function ($item) use($stageId) {
            return $item->id == $stageId;
        });

        return $stageIndex !== false ? $this->_stages[$stageIndex] : $this->_stages[0];
    }

    public function getStageName()
    {
        return $this->getStage()->title;
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        $commands['get_task'] = $bitrix->buildCommand('tasks.task.get', [
            'select' => collect(static::mapFields())->keys()->toArray(),
            'taskId' => $id,
        ]);

        $commands['get_project'] = $bitrix->buildCommand('sonet_group.get', ['FILTER' => ['ID' => '$result[get_task][task][groupId]']]);
        $commands['get_responsible'] = $bitrix->buildCommand('user.get', ['ID' => '$result[get_task][task][responsibleId]']);
        $commands['get_stages'] = $bitrix->buildCommand('task.stages.get', ['entityId' => '$result[get_task][task][groupId]']);
        $commands['get_checklist'] = $bitrix->buildCommand('task.checklistitem.getlist', ['taskId' => '$result[get_task][task][id]']);

        ['result' => ['result' => $response]] = $bitrix->batchRequest($commands);

        if($model->load(['Task' => $response['get_task']['task']]) && $model->validate())
        {
            $project = new Project();
            $model->_project = Project::collect($project, $response['get_project'][0]);

            $user = new User();
            $model->responsible = User::collect($user, $response['get_responsible'][0]);

            $model->_stages = collect(Stage::multipleCollect(new Stage(), $response['get_stages']))->values()->toArray();
            $model->_checklist = CheckListItem::multipleCollect(new CheckListItem(), $response['get_checklist']);

            $model->_checklist = collect($model->_checklist)->groupBy(function ($item){
                return !empty($item->parentId) ? $item->parentId : $item->id;
            })->toArray();

            $model->_checklist = collect($model->_checklist)->transform(function ($item){
                return collect($item)->sortBy('parentId');
            })->toArray();

            return $model;
        }

        return false;
    }

    public function getCompanyId()
    {
        $indexCompanyRow = collect($this->ufCrmTask)->search(function ($item){
            return u($item)->containsAny('CO');
        });

        return $indexCompanyRow !== false ? u($this->ufCrmTask[$indexCompanyRow])->after('CO_')->toString() : false;
    }
}
