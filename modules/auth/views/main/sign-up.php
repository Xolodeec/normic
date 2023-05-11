<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Регистрация";

?>

<div class="wrapper-form shadow-sm rounded mb-4">
    <h4 class="mb-4 text-center text-uppercase">Регистрация</h4>
    <?php $form = ActiveForm::begin([
        'id' => 'sign-up-form',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}",
        ],
    ]) ?>

    <?= $form->field($model, 'typeCompany', ['options' => ['class' => 'form-floating mb-3 type-company-select']])->dropDownList(\yii\helpers\ArrayHelper::map($presets, 'id', 'name'), ['prompt' => 'Выберите тип']); ?>
    <?= $form->field($model, 'titleCompany', ['options' => ['class' => 'form-floating mb-3']])->textInput(); ?>
    <?= $form->field($model, 'inn', ['options' => ['class' => 'form-floating mb-3 inn']])->textInput(); ?>
    <?= $form->field($model, 'ogrn', ['options' => ['class' => 'form-floating mb-3 hide-block ogrn']])->textInput(); ?>
    <?= $form->field($model, 'ogrnIp', ['options' => ['class' => 'form-floating mb-3 hide-block ogrnIp']])->textInput(); ?>
    <?= $form->field($model, 'phone', ['options' => ['class' => 'form-floating mb-3']])->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '+7 (999) 999 99 99',
    ]); ?>
    <?= $form->field($model, 'telegramPhone', ['options' => ['class' => 'form-floating mb-3']])->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '+7 (999) 999 99 99',
    ]); ?>
    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary w-100']) ?>
    <?php $form::end() ?>
</div>

<div class="auth-toolbar text-center">
    <?= Html::a('Есть уже аккаунт?', 'login'); ?>
</div>
