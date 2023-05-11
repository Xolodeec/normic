<?php

namespace app\models\entity;

use app\models\bitrix\Bitrix;
use app\models\bitrix\crm\Company;
use app\models\bitrix\tasks\Project;
use yii\base\BaseObject;

class Contact extends \app\models\bitrix\crm\Contact
{
    public $password;
    public $company;

    public static function findById($id)
    {
        $model = new static();
        $bitrix = new Bitrix();

        $commands['get_contact'] = $bitrix->buildCommand('crm.contact.list', [
            'filter' => ['=ID' => $id],
            'start' => -1,
            'select' => collect(static::mapFields())->keys()->toArray(),
        ]);

        $commands['get_company'] = $bitrix->buildCommand('crm.company.get', ['ID' => '$result[get_contact][0][COMPANY_ID]']);

        ['result' => ['result' => $result]] = $bitrix->batchRequest($commands);

        if(!empty($result['get_contact']) && static::collect($model, $result['get_contact'][0]))
        {
            if(!empty($result['get_company'])) $model->company = Company::collect(new Company(), $result['get_company']);

            return $model;
        }

        return false;
    }

    public function rules()
    {
        $rules = collect(parent::rules());
        $rules->push(['password', 'string']);

        return $rules->toArray();
    }

    public static function mapFields()
    {
        $mapFields = collect(parent::mapFields());
        $mapFields->put('UF_CRM_1681819367233', 'password');

        return $mapFields->toArray();
    }
}
