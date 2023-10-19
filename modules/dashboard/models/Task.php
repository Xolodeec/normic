<?php

namespace app\modules\dashboard\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use function Symfony\Component\String\u;

class Task extends Model
{
    public $id;
    public $title;
    public $description;
    public $status;
    public $groupId;
    public $stageId;
    public $createdBy;
    public $createdDate;
    public $responsibleId;
    public $accomplice;
    public $auditor;
    public $deadline;
    public $ufCrmTask;
    public $statusName;
    public $statusColor;
    public $startDatePlan;
    public $endDatePlan;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'status', 'groupId', 'stageId', 'createdBy', 'responsibleId'], 'number'],
            [['title', 'description', 'startDatePlan', 'endDatePlan'], 'string'],
            [['ufCrmTask'], 'default', 'value' => []],
            [['createdDate'], 'safe'],
            ['deadline', 'filter', 'filter' => function($item){
                return !is_null($item) ? date("Y-m-d", strtotime($item)) : $item;
            }],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'TITLE' => 'title',
            'DESCRIPTION' => 'description',
            'STATUS' => 'status',
            'GROUP_ID' => 'groupId',
            'STAGE_ID' => 'stageId',
            'CREATED_BY' => 'createdBy',
            'CREATED_DATE' => 'createdDate',
            'RESPONSIBLE_ID' => 'responsibleId',
            'ACCOMPLICES' => 'accomplice',
            'AUDITORS' => 'auditor',
            'DEADLINE' => 'deadline',
            'UF_CRM_TASK' => 'ufCrmTask',
            'START_DATE_PLAN' => 'startDatePlan',
            'END_DATE_PLAN' => 'endDatePlan',
        ];
    }

    public static function getList($filter = [], $start = -1)
    {
        $result = new Collection();
        $bitrix = new Bitrix();

        $taskId = 0;
        $finish = false;

        while(!$finish)
        {
            $filter['>ID'] = $taskId;

            ['result' => ['tasks' => $tasks]] = $bitrix->request('tasks.task.list', [
                'select' => collect(static::mapFields())->keys()->toArray(),
                'order' => ['ID' => 'ASC'],
                'filter' => $filter,
                'start' => $start,
            ]);

            if(collect($tasks)->isNotEmpty())
            {
                $taskId = $tasks[count($tasks) - 1]['id'];
                $result = $result->merge(static::multipleLoad($tasks));
            }
            else
            {
                $finish = true;
            }
        }

        return $result;
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => ['task' => $task]] = $bitrix->request('tasks.task.get', [
            'select' => collect(static::mapFields())->keys()->toArray(),
            'taskId' => $id,
        ]);

        if($model->load(['Task' => $task]) && $model->validate())
        {
            return $model;
        }

        return false;
    }

    public static function multipleLoad($data)
    {
        $result = new Collection();

        foreach ($data as $index => $task)
        {
            $model = new static();

            if($model->load(['Task' => $task]) && $model->validate())
            {
                $result->push($model);
            }
        }

        return $result->toArray();
    }

    public function create()
    {
        $bitrix = new Bitrix();
        $fields = static::getParamsField($this);

        return $bitrix->request('tasks.task.add', ['fields' => $fields]);
    }

    public function update()
    {
        $bitrix = new Bitrix();
        $fields = static::getParamsField($this);

        return $bitrix->request('tasks.task.update', ['taskId' => $this->id, 'fields' => $fields]);
    }

    public function getDurationWeek()
    {
        return (date('W', strtotime($this->endDatePlan)) - date('W', strtotime($this->startDatePlan))) === 0 ? 1 : date('W', strtotime($this->endDatePlan)) - date('W', strtotime($this->startDatePlan));
    }
}