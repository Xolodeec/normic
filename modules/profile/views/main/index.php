<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Профиль";

$this->params['breadcrumbs'][] = $this->title;

?>

<article class="profile-form-block">
    <header>
        <h4>ФИО</h4>
    </header>
    <?php $form = ActiveForm::begin([
        'id' => 'contactr-form',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}",
        ],
    ]) ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'lastName', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>
    <?= $form->field($model, 'name', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>
    <?= $form->field($model, 'secondName', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?php $form::end() ?>
</article>

<article class="profile-form-block">
    <header>
        <h4>Общая информация</h4>
        <p>Краткая информация о вас</p>
    </header>
    <ul class="company-info">
        <li>Название компании: <?= Yii::$app->user->identity->company->title ?></li>
        <li>Мой телефон: <?= !empty(Yii::$app->user->identity->phone) ? Yii::$app->user->identity->phone[0]['VALUE'] : "Не указан" ?></li>
        <li>Мой email: <?= !empty(Yii::$app->user->identity->email) ? Yii::$app->user->identity->email[0]['VALUE'] : "Не указан" ?></li>
    </ul>
</article>