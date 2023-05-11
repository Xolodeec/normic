<?php

namespace app\modules\profile\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\crm\requisite\BankRequisite;
use Tightenco\Collect\Support\Collection;

class Requisite extends \app\models\bitrix\crm\requisite\Requisite
{
    public $bankDetails;

    public static function findByCompanyId($companyId)
    {
        $bitrix = new Bitrix;
        $model = new static();
        $commands = new Collection();

        $commands->put('get_requisite', $bitrix->buildCommand('crm.requisite.list', ['filter' => ['ENTITY_TYPE_ID' => 4, 'ENTITY_ID' => $companyId]]));
        $commands->put('get_bank_details', $bitrix->buildCommand('crm.requisite.bankdetail.list', ['filter' => ['ENTITY_ID' => '$result[get_requisite][0][ID]']]));

        ['result' => ['result' => $result]] = $bitrix->batchRequest($commands->toArray());

        $result = collect($result);

        if(!empty($result->get('get_requisite')[0]) && $model::collect($model, $result->get('get_requisite')[0]))
        {
            $bankDetails = new BankRequisite();

            if(!empty($result->get('get_bank_details')[0]))
            {
                $bankDetails::collect($bankDetails, $result->get('get_bank_details')[0]);
            }

            $model->bankDetails = $bankDetails;

            return $model;
        }

        return false;
    }
}
