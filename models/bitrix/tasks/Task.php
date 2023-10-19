<?php

namespace app\models\bitrix\tasks;

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

    use Collector;

    public function rules()
    {
        return [
            [['id', 'status', 'groupId', 'stageId', 'createdBy', 'responsibleId'], 'number'],
            [['title', 'description'], 'string'],
            [['ufCrmTask'], 'default', 'value' => []],
            [['createdDate'], 'safe'],
            ['deadline', 'filter', 'filter' => function($item){
                return !is_null($item) ? date("Y-m-d", strtotime($item)) : $item;
            }],
        ];
    }

    public function getParsedDescription()
    {
        $configurator = new \s9e\TextFormatter\Configurator;

        $configurator->BBCodes->addCustom(
            '[P]{TEXT}[/P]',
            '<p>{TEXT}</p>'
        );
        
        $configurator->BBCodes->addFromRepository('B');
        $configurator->BBCodes->addFromRepository('I');
        $configurator->BBCodes->addFromRepository('URL');


        // Get an instance of the parser and the renderer
        extract($configurator->finalize());

        $xml  = $parser->parse($this->description);
        $html = $renderer->render($xml);

        return $this->parserAttachments($html);

        /*$xml =  \s9e\TextFormatter\Bundles\Forum::parse($this->description);

        return \s9e\TextFormatter\Bundles\Forum::render($xml);*/
    }

    public function parserAttachments($description)
    {
        $bitrix = new Bitrix();

        $tempDescription = $description;

        $substrCount = substr_count($description, 'DISK FILE');
        $batch = [];

        for ($i = 0; $i < $substrCount; $i++)
        {
            if(u($tempDescription)->containsAny('[DISK FILE ID='))
            {
                $tempDescription = u($tempDescription)->after('[DISK FILE ID=n');

                $batch[] = $bitrix->buildCommand('disk.attachedObject.get', ['id' => $tempDescription->before(']')->toString()]);
                $tempDescription = $tempDescription->after(']')->toString();
            }
        }

        ['result' => ['result' => $response]] = $bitrix->batchRequest($batch);

        if(!empty($response))
        {
            for ($i = 0; $i < $substrCount; $i++)
            {
                $description = u($description)->replace("[DISK FILE ID={$response[$i]['ID']}]", "<img src='{$response[$i]['DOWNLOAD_URL']}'>")->toString();
            }
        }

        return $description;
    }

    public function afterValidate()
    {
        parent::afterValidate();

        if($this->status == 2) $this->statusName = 'В очереди';
        if($this->status == 3) $this->statusName = 'Выполняется';
        if($this->status == 4) $this->statusName = 'Контроль';
        if($this->status == 5) $this->statusName = 'Завершена';
        if($this->status == 6) $this->statusName = 'Отложена';

        if($this->status == 2) $this->statusColor = 'b1b1b1';
        if($this->status == 3 || $this->status == 4) $this->statusColor = 'fdb66d';
        if($this->status == 5) $this->statusColor = '5bc159';
        if($this->status == 6) $this->statusColor = 'f18f98';
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
            'UF_CRM_TASK' => 'ufCrmTask'
        ];
    }

    public static function getList($filter = [], $start = -1)
    {
        $result = new Collection();
        $bitrix = new Bitrix();

        ['result' => ['tasks' => $tasks]] = $bitrix->request('tasks.task.list', [
            'select' => collect(static::mapFields())->keys()->toArray(),
            'order' => ['ID' => 'DESK'],
            'filter' => $filter,
            'start' => $start,
        ]);

        if(collect($tasks)->isNotEmpty())
        {
            $result = static::multipleLoad($tasks);
        }

        return $result->toArray();
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
}