<?php

namespace app\modules\tasks\models;

use app\models\bitrix\Bitrix;
use function Symfony\Component\String\u;

class Project extends \app\models\bitrix\tasks\Project
{
    public static function getProjectByCompany($companyName)
    {
        $bitrix = new Bitrix();
        ["result" => $result] = $bitrix->request('sonet_group.get', ['FILTER' => ['%NAME' => $companyName]]);

        return static::multipleCollect(new static(), $result);
    }

    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => function($item){
                return u($item)->after(']')->trim()->toString();
            }]
        ];
    }
}
