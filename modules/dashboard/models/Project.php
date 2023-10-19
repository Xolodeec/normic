<?php

namespace app\modules\dashboard\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use app\models\bitrix\User;
use yii\base\Model;

class Project extends Model
{
    public $id;
    public $name;
    public $createdDate;
    public $users;

    use Collector;

    public function rules()
    {
        return [
            ['id', 'number'],
            [['name', 'createdDate'], 'string']
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'name',
            'DATE_CREATE' => 'createdDate',
        ];
    }

    public static function findById($id)
    {
        $bitrix = new Bitrix();

        $commands['group'] = $bitrix->buildCommand('sonet_group.get', ['FILTER' => ['ID' => $id]]);
        $commands['user_group'] = $bitrix->buildCommand('sonet_group.user.get', ['ID' => $id, 'SONET_ROLES_USER' => 'K']);

        ['result' => ['result' => $result]] = $bitrix->batchRequest($commands);

        $model = (new static())::collect(new static(), $result['group'][0]);

        $userIds = collect($result['user_group'])->filter(function ($userData){
            return $userData["ROLE"] !== 'A';
        })->map(function ($item){
            return $item['USER_ID'];
        })->toArray();;

        ['result' => $result] = $bitrix->request('user.get', ['filter' => ['=ID' => $userIds]]);

        $model->users = (new User())::multipleCollect(new User(), $result);

        return $model;
    }
}