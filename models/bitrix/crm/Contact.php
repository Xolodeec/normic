<?php

namespace app\models\bitrix\crm;

use app\models\bitrix\Bitrix;
use app\models\bitrix\traits\Collector;
use Tightenco\Collect\Support\Collection;
use yii\base\Model;
use function Symfony\Component\String\u;

class Contact extends Model
{
    public $id;
    public $name;
    public $secondName;
    public $lastName;
    public $phone;
    public $companyId;
    public $email;

    use Collector;

    public function rules()
    {
        return [
            [['id', 'companyId'], 'number'],
            [['name', 'secondName', 'lastName'], 'string'],
            [['phone', 'email'], 'default', 'value' => []],
        ];
    }

    public static function mapFields()
    {
        return [
            'ID' => 'id',
            'NAME' => 'name',
            'SECOND_NAME' => 'secondName',
            'LAST_NAME' => 'lastName',
            'PHONE' => 'phone',
            'COMPANY_ID' => 'companyId',
            'EMAIL' => 'email',
        ];
    }

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('crm.contact.list', [
            'filter' => ['=ID' => $id],
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        if(!empty($result) && static::collect($model, $result[0]))
        {
            return $model;
        }

        return false;
    }

    public static function findByPhone($phone)
    {
        $model = new static();
        $bitrix = new Bitrix();

        ['result' => $result] = $bitrix->request('crm.contact.list', [
            'filter' => ['PHONE' => $phone],
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        if(!empty($result) && static::collect($model, $result[0]))
        {
            return $model;
        }

        return false;
    }

    public function save()
    {
        $bitrix = new Bitrix;

        return $bitrix->request('crm.contact.update', ['id' => $this->id, 'fields' => static::getParamsField($this)]);
    }
}
