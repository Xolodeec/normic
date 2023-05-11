<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Мои проекты";

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="wrapper-block mt-3">
    <table class="default-table">
        <thead>
        <tr>
            <th>Название проекта</th>
            <th>Дата создания</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($projects)) : ?>
            <?php foreach($projects as $index => $project) : ?>
                <tr>
                    <td><?= $project->name ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($project->createdDate)) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="2">Ничего не найдено</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>