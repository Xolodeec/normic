<?php

namespace app\modules\dashboard\models;

use app\models\bitrix\Bitrix;
use yii\base\Model;
use app\modules\dashboard\models\Task;
use app\modules\dashboard\models\Project;

class Dashboard extends Model
{
    public $interval;
    public $date;
    public $header;
    public $body;
    public $freeTasks;

    public function rules()
    {
        return [
            ['interval', 'required'],
            ['interval', 'number'],
            ['date', 'string'],
            ['date', 'default', 'value' => date('Y-m-d')],
            ['freeTasks', 'default', 'value' => []],
        ];
    }

    public function create()
    {
        if($this->interval == 0)
        {
            $this->header = $this->createYearlyHeader();
            $this->body = $this->createYearlyBody();
        }

        if($this->interval == 1)
        {
            $this->header = $this->createQuarterHeader();
            $this->body = $this->createQuarterBody();
        }
    }

    public function createYearlyHeader()
    {
        $startDate = $this->date;
        $endDate = date('Y-m-d', strtotime('+1 year', strtotime($startDate)));

        $monthNumberEndDate = date('n', strtotime($endDate));
        $yearNumberEndDate = date('y', strtotime($endDate));
        $finish = false;

        $result = [];

        while(!$finish)
        {
            $monthNumberStartDate = date('n', strtotime($startDate));
            $yearNumberStartDate = date('y', strtotime($startDate));

            if($monthNumberStartDate === $monthNumberEndDate && $yearNumberStartDate === $yearNumberEndDate)
            {
                $finish = true;
            }
            else
            {
                $currentDate = date('Y-m-d', strtotime($startDate));

                $result['month'][] = $currentDate;

                $startDate = date('Y-m-d', strtotime("+1 month", strtotime($startDate)));
            }
        }

        foreach ($result['month'] as $index => $month)
        {
            $currentYear = date('Y', strtotime($month));
            $nextYear = date('Y', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));

            if($index === 0)
            {
                $startNumberWeek = date('W', strtotime($month));
                $endNumberWeek = date('W', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));
            }
            elseif ($index === count($result['month']) - 1)
            {
                $startNumberWeek = date('W', strtotime('first day of this month', strtotime($month)));
                $endNumberWeek = date('W', strtotime($month));
            }
            else
            {
                $startNumberWeek = date('W', strtotime('first day of this month', strtotime($month)));
                $endNumberWeek = date('W', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));
            }

            if($currentYear !== $nextYear)
            {
                $endNumberWeek = date('W', strtotime('last day of this month', strtotime($month))) + 1;
            }

            for($i = $startNumberWeek; $i < $endNumberWeek; $i++)
            {
                $result['week'][$month][] = $i;
            }
        }

        return $result['week'];
    }

    public function createYearlyBody()
    {
        $project = Project::findById(57);
        $tasks = Task::getList(['GROUP_ID' => 57, '>START_DATE_PLAN' => date('Y-m-d 00:00', strtotime($this->date)), '<=END_DATE_PLAN' => date('Y-m-d 00:00',strtotime('+1 year',  strtotime($this->date)))]);

        $groupedWeekByYear = collect($this->header)->groupBy(function ($item, $key){
            return date('Y', strtotime($key));
        })->map(function ($item){
            return collect($item)->flatten(1)->flip();
        })->map(function ($item){
            return collect($item)->map(function ($week){
               return [];
            });
        })->toArray();

        foreach ($project->users as $index => $user)
        {
            $body[$user->id] = [
                'user' => $user,
                'tasks' => $groupedWeekByYear,
            ];
        }

        foreach ($tasks as $index => $task)
        {
            if(collect($body)->has($task->responsibleId))
            {
                $year = date('Y', strtotime($task->startDatePlan));
                $week = date('W', strtotime($task->startDatePlan));

                $body[$task->responsibleId]['tasks'][$year][$week][] = $task;
            }
            else
            {
                $this->freeTasks[] = $task;
            }
        }


        return $body;
    }

    public function createQuarterHeader()
    {
        $startDate = $this->date;
        $endDate = date('Y-m-d', strtotime('+4 month', strtotime($startDate)));

        $monthNumberEndDate = date('n', strtotime($endDate));
        $yearNumberEndDate = date('y', strtotime($endDate));
        $finish = false;

        $result = [];

        while(!$finish)
        {
            $monthNumberStartDate = date('n', strtotime($startDate));
            $yearNumberStartDate = date('y', strtotime($startDate));

            if($monthNumberStartDate === $monthNumberEndDate && $yearNumberStartDate === $yearNumberEndDate)
            {
                $finish = true;
            }
            else
            {
                $currentDate = date('Y-m-d', strtotime($startDate));

                $result['month'][] = $currentDate;

                $startDate = date('Y-m-d', strtotime("+1 month", strtotime($startDate)));
            }
        }

        foreach ($result['month'] as $index => $month)
        {
            $currentYear = date('Y', strtotime($month));
            $nextYear = date('Y', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));

            if($index === 0)
            {
                $startNumberWeek = date('W', strtotime($month));
                $endNumberWeek = date('W', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));
            }
            elseif ($index === count($result['month']) - 1)
            {
                $startNumberWeek = date('W', strtotime('first day of this month', strtotime($month)));
                $endNumberWeek = date('W', strtotime($month));
            }
            else
            {
                $startNumberWeek = date('W', strtotime('first day of this month', strtotime($month)));
                $endNumberWeek = date('W', strtotime('+1 month', strtotime('first day of this month', strtotime($month))));
            }

            if($currentYear !== $nextYear)
            {
                $endNumberWeek = date('W', strtotime('last day of this month', strtotime($month))) + 1;
            }

            for($i = $startNumberWeek; $i < $endNumberWeek; $i++)
            {
                $result['week'][$month][] = $i;
            }
        }

        return $result['week'];
    }

    public function createQuarterBody()
    {
        $project = Project::findById(57);
        $tasks = Task::getList(['GROUP_ID' => 57, '<=END_DATE_PLAN' => date('Y-m-d 00:00',strtotime('+4 month',  strtotime($this->date)))]);

        $groupedWeekByYear = collect($this->header)->groupBy(function ($item, $key){
            return date('Y', strtotime($key));
        })->map(function ($item){
            return collect($item)->flatten(1)->flip();
        })->map(function ($item){
            return collect($item)->map(function ($week){
                return [];
            });
        })->toArray();

        foreach ($project->users as $index => $user)
        {
            $body[$user->id] = [
                'user' => $user,
                'tasks' => $groupedWeekByYear,
            ];
        }

        foreach ($tasks as $index => $task)
        {
            $year = date('Y', strtotime($task->startDatePlan));
            $week = date('W', strtotime($task->startDatePlan));

            $body[$task->responsibleId]['tasks'][$year][$week][] = $task;
        }

        return $body;
    }

    public function getListInterval()
    {
        return ['Ежегодная', 'Ежеквартальная'];
    }
}