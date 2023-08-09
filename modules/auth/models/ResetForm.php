<?php

namespace app\modules\auth\models;

use app\models\bitrix\Bitrix;
use Tightenco\Collect\Support\Collection;
use Yii;
use yii\base\Model;
use app\models\entity\Contact;

class ResetForm extends Model
{
    public $phone;

    public function rules()
    {
        return [
            ['phone', 'required'],
            [['phone'], 'filter', 'filter' => function($item){
                return preg_replace('/[^0-9+]/', '', $item);
            }],
            //['phone', 'validationPhone'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
        ];
    }

    /*public function validationPhone($attribute)
    {
        if(empty(School::findDuplicateByPhone($this->$attribute)))
        {
            $this->addError($attribute, 'Пользователь с таким номером не зарегистрирован.');
        }
    }*/

    public function reset()
    {
        $bitrix = new Bitrix;
        $commands = new Collection();
        $password = \Yii::$app->security->generateRandomString(5);

        $contact = Contact::findByPhone($this->phone);
        $contact->password = \Yii::$app->security->generatePasswordHash($password);

        $commands->put('company_update', $bitrix->buildCommand('crm.contact.update', ['ID' => $contact->id, 'fields' => $contact::getParamsField($contact)]));
        $commands->put('start_bizproc', $bitrix->buildCommand('bizproc.workflow.start', [
            'TEMPLATE_ID' => 129,
            'DOCUMENT_ID' => ['crm', 'CCrmDocumentContact', $contact->id],
            'PARAMETERS' => [
                'password' => $password,
            ],
        ]));

        return $bitrix->batchRequest($commands->toArray());
    }
}