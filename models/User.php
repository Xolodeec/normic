<?php

namespace app\models;

use app\models\bitrix\Bitrix;
use app\models\entity\Contact;
use Yii;
use yii\base\BaseObject;

class User extends BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $companyId;
    public $name;
    public $lastName;
    public $secondName;
    public $phone;
    public $password;
    public $company;
    public $authKey;
    public $accessToken;
    public $email;

    public static function findIdentity($id)
    {
        $contact = Contact::findById($id);

        if($contact)
        {
            return User::load($contact);
        }

        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByPhone($phone)
    {
        $contact = Contact::findByPhone($phone);

        if($contact)
        {
            return User::load($contact);
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function load($contact)
    {
        $user = new static();
        $user->id = $contact->id;
        $user->lastName = $contact->lastName;
        $user->name = $contact->name;
        $user->secondName = $contact->secondName;
        $user->password = $contact->password;
        $user->companyId = $contact->companyId;
        $user->company = $contact->company;
        $user->phone = $contact->phone;
        $user->email = $contact->email;

        return $user;
    }
}
