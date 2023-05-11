<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Редактирование задачи";

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="wrapper-task-from">
    <?php $form = ActiveForm::begin([
        'id' => 'create-task-form',
        'fieldConfig' => [
            'enableClientValidation' => false,
            'template' => "{input}{label}",
        ],
    ]) ?>

    <?= $form->field($model, 'title', ['options' => ['class' => 'form-floating mb-3']])->textInput() ?>
    <?= $form->field($model, 'groupId', ['options' => ['class' => 'form-floating mb-3']])->dropDownList(\yii\helpers\ArrayHelper::map($model->getListProject(), 'id', 'name'), ['prompt' => 'Выбрать проект']) ?>
    <?= $form->field($model, 'description', ['options' => ['class' => 'mb-3']])->widget(\franciscomaya\sceditor\SCEditor::class, [
        'options' => [
            'rows' => 12
        ],
        'clientOptions' => [
            'plugins' => 'bbcode',
        ]
    ])->label(false) ?>

    <?= $form->field($model, 'deadline', ['options' => ['class' => 'form-floating mb-3']])->textInput(['type' => 'date']) ?>

    <?= Html::submitButton('Сохранить изменения', ['class' => ['btn btn-success']]) ?>

    <?php $form::end() ?>
</div>
