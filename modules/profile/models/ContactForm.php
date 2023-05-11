<?php

namespace app\modules\profile\models;

use app\models\bitrix\Bitrix;
use app\models\bitrix\crm\Contact;
use yii\base\Model;

class ContactForm extends Model
{
    public $id;
    public $name;
    public $lastName;
    public $secondName;

    public function rules()
    {
        return [
            [['name', 'lastName', 'secondName', 'id'], 'string'],
            [['name', 'lastName', 'secondName'], 'filter', 'filter' => function($item){
                return trim($item);
            }],
            ['name', 'required'],
        ];
    }

    public static function instanceByUser($userId)
    {
        $contact = Contact::findById($userId);

        $model = new static();
        $model->name = $contact->name;
        $model->lastName = $contact->lastName;
        $model->secondName = $contact->secondName;

        return $model;
    }

    public function updateByUser($userId)
    {
        $contact = Contact::findById($userId);
        $contact->name = $this->name;
        $contact->lastName = $this->lastName;
        $contact->secondName = $this->secondName;

        return $contact->save();
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'lastName' => 'Фамилия',
            'secondName' => 'Отчество',
        ];
    }
}
