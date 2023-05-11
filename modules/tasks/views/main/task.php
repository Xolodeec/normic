<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = $task->title;

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="toolbar mt-3 mb-3">
    <?= Html::a('Редактировать', \yii\helpers\Url::to(['/tasks/main/edit', 'id' => $task->id]), ['class' => 'btn btn-success']) ?>
</div>

<div class="container-fluid g-0">
    <div class="row wrapper-task">
        <div class="col-md-8">
            <div class="wrapper-block task-block">
                <header>
                    <?= $task->title ?>
                </header>
                <article>
                    <?= $task->getParsedDescription(); ?>
                </article>
                <?php if(!empty($task->_checklist)) : ?>
                <footer>
                        <?php foreach ($task->_checklist as $items) : ?>
                            <div class="checlkist grey-wrapper">
                            <?php foreach ($items as $item) : ?>
                                <?php if(empty($item->parentId)) : ?>
                                    <h4><?= $item->title ?></h4>
                                <?php else: ?>
                                    <p class="d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminateDisabled" disabled <?= $item->isCompleted() ? "checked" : "" ?>>
                                        <?php if($item->isCompleted()) : ?>
                                            <s><?= $item->title ?></s>
                                        <?php else: ?>
                                        <?= $item->title ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                </footer>
                <?php endif; ?>
            </div>
            <div class="wrapper-comment">
                <h4>Комментарии</h4>

                <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                                'template' => '{input}',
                        ],
                ]) ?>
                <?= $form->field($model, 'message')->textarea() ?>
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                <?php $form::end() ?>

                <?php if(!empty($comments)) : ?>
                    <?php foreach ($comments as $index => $comment) : ?>
                    <div class="comment" id="<?= $comment->id ?>">
                        <header><?= $comment->authorName ?> (<?= date("d.m.Y H:i", strtotime($comment->date)) ?>)</header>
                        <article><?= $comment->message ?></article>
                        <?php if($comment->authorId == 55) :?>
                        <footer>
                            <a href="javascript::void()" class="btn-edit-comment" data-bs-toggle="modal" data-bs-target="#editCommentModal">Редактировать</a> |
                            <a href="javascript::void()" class="btn-delete-comment" data-bs-toggle="modal" data-bs-target="#deleteCommentModal">Удалить</a>
                        </footer>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wrapper-block">
                <ul class="task-info">
                    <li><b>Проект:</b> <?= $task->getNameProject()?></li>
                    <li><b>Статус:</b> <span class="bg-blue" style="background: #<?= $task->statusColor ?>; color: #fff; font-size: 12px;"><?= $task->statusName ?><span></li>
                    <li><b>Стадия:</b> <span class="bg-blue" style="background: #<?= $task->getStage()->color ?>; color: #fff; font-size: 12px;"><?= $task->getStageName() ?><span></li>
                    <li><b>Крайний срок:</b> <?= date("d.m.Y", strtotime($task->deadline)) ?></li>
                    <li><b>Ответственный:</b> <?= "{$task->responsible->lastName} {$task->responsible->name}" ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Изменение комментария</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'update-comment',
                    'action' => \yii\helpers\Url::to(['/tasks/main/update-comment', 'id' => $task->id]),
                    'fieldConfig' => [
                        'template' => '{input}',
                    ],
                ]) ?>

                <?= $form->field($model, 'taskId', ['options' => ['class' => 'mb-0']])->hiddenInput(['value' => $task->id]) ?>
                <?= $form->field($model, 'id', ['options' => ['class' => 'mb-0 comment-id']])->hiddenInput() ?>
                <?= $form->field($model, 'message', ['options' => ['class' => 'mb-0']])->textarea() ?>
                <?php $form::end() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success save-edit-comment">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Вы уверены, что хотите удалить комментарий?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'delete-comment',
                'action' => \yii\helpers\Url::to(['/tasks/main/delete-comment', 'id' => $task->id]),
                'fieldConfig' => [
                    'template' => '{input}',
                ],
            ]) ?>

            <?= $form->field($model, 'taskId', ['options' => ['class' => 'mb-0']])->hiddenInput(['value' => $task->id]) ?>
            <?= $form->field($model, 'id', ['options' => ['class' => 'mb-0 comment-id']])->hiddenInput() ?>
            <?php $form::end() ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
                <button type="button" class="btn btn-danger delete-comment">Да</button>
            </div>
        </div>
    </div>
</div>