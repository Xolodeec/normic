<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Список задач";

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="toolbar mt-3">
    <?= Html::a('Создать задачу', '/tasks/main/create', ['class' => 'btn btn-success']) ?>
</div>

<div class="wrapper-block mt-3">
    <table class="default-table">
        <thead>
        <tr>
            <th class="text-center column-small">#</th>
            <th class="title-task">Название задачи</th>
            <th class="project-task">Проект</th>
            <th class="responsible-task">Ответственный</th>
            <th class="create-date-task">Дата создания</th>
            <th class="status-task">Статус</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($model->tasks)) : ?>
            <?php foreach($model->tasks as $index => $task) : ?>
                <tr>
                    <td class="text-center"><span class="bg-blue"><?= $index ?><span></td>
                    <td class="title-task"><?= Html::a($task->title, \yii\helpers\Url::to(['task', 'id' => $task->id])) ?></td>
                    <td class="project-task"><?= $task->getNameProject() ?></td>
                    <td class="responsible-task"><?= "{$task->responsible->lastName} {$task->responsible->name}" ?></td>
                    <td class="create-date-task"><?= date('d.m.Y H:i', strtotime($task->createdDate)) ?></td>
                    <td class="status-task"><span class="bg-blue" style="background: #<?= $task->statusColor ?>; color: #fff; font-size: 12px;"><?= $task->statusName ?><span></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="7">Ничего не найдено</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>