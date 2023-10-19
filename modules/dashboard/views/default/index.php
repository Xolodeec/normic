<div class="container-fluid p-0">
    <div class="row g-0 d-flex vh-100">
        <div class="col-9">
            <div class="wp-dashboard h-100">
                <div class="wp-filter">
                    <?php $form = \yii\bootstrap5\ActiveForm::begin() ?>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-2">
                                <?= $form->field($model, 'interval')->dropDownList($model->getListInterval(), ['prompt' => 'Выбрать'])->label(false); ?>
                            </div>
                            <div class="col-2">
                                <?= $form->field($model, 'date')->textInput(['type' => 'date'])->label(false); ?>
                            </div>
                            <div class="col-auto">
                                <?= \yii\bootstrap5\Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-success" onclick="BX24.openPath('/workgroups/group/57/tasks/task/edit/0/?SCOPE=tasks_grid&GROUP_ID=57')">Запланировать выезд</button>
                            </div>
                        </div>
                    </div>
                    <?php $form::end(); ?>
                </div>
                <?php if (!empty($model->header)) : ?>
                    <div class="dashboard">
                        <div class="wp-table">
                            <table>
                                <thead>
                                <tr class="title-row">
                                    <th rowspan="2">
                                        Тест
                                    </th>
                                    <?php foreach ($model->header as $month => $weeks) : ?>
                                        <th colspan="<?= count($model->header[$month]) ?>"><?= date('F', strtotime($month))?></th>
                                    <?php endforeach;?>
                                </tr>
                                <tr class="subtitle-row">
                                    <?php foreach ($model->header as $month => $weeks) : ?>
                                        <?php foreach ($weeks as $index => $week) : ?>
                                            <th class="d-column">
                                                <div class="d-column"><?= $week ?></div>
                                            </th>
                                        <?php endforeach;?>
                                    <?php endforeach;?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($model->body as $index => $data) : ?>
                                    <tr>
                                        <td><p class="responsible"><?= "{$data['user']->lastName} {$data['user']->name}" ?></p></td>
                                        <?php foreach ($data['tasks'] as $year => $weeks) : ?>
                                            <?php foreach ($weeks as $numberWeek => $tasks) : ?>
                                                <td class="cell-task">
                                                    <?php foreach ($tasks as $numberTask => $task) : ?>
                                                        <div class="task resizable selector" style="position: absolute; top: <?= $numberTask * 40?>px;left: 0; width: <?= ($task->getDurationWeek() * 111 - 1) ?>px; font-size: 9px !important;" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<?= $task->title ?>" id="<?= $task->id ?>" group-id="<?= $task->groupId ?>" startdateplan="<?= $task->startDatePlan ?>" enddateplan="<?= $task->endDatePlan ?>">
                                                            <?= $task->title ?>
                                                        </div>
                                                    <?php endforeach;?>
                                                </td>
                                            <?php endforeach;?>
                                        <?php endforeach;?>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-3 right-sidebar">
            <h4>Нераспределенные задачи</h4>
            <div class="list-freetasks">
                <?php if (!empty($model->freeTasks)) : ?>
                    <?php foreach ($model->freeTasks as $index => $task) :?>
                        <div class="task selector" data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="<?= $task->title ?>" id="<?= $task->id ?>" group-id="<?= $task->groupId ?>" startdateplan="<?= $task->startDatePlan ?>" enddateplan="<?= $task->endDatePlan ?>">
                            <?= $task->title ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-resizable" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждаете изменения?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary btn-access">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-draggable" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждаете изменения?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary btn-access">Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
    @import url(https://fonts.googleapis.com/css?family=Raleway);

    body{
        font-family: 'Inter', sans-serif;
        background: #fff;
        line-height: 24px;
        font-size: 14px;
        color: #101828;
        font-weight: 600;
    }

    .wp-table{
        overflow-x: scroll;
        overflow-y: hidden;
    }

    .wp-table table{
        border-collapse: collapse;
    }

    .wp-table table th, .wp-table table td{
        border: 1px solid #cbcaca !important;
        box-sizing: content-box !important;
    }

    .wp-table table th{
        padding: 10px 5px;
        text-align: center;
    }

    .wp-table table tbody td{
        padding: 0 !important;
        height: 80px;
    }

    .d-column{
        width: 100px !important;
    }

    .responsible{
        width: 200px;
    }

    .wp-table table tbody td:first-child{
        text-align: center;
    }

    .cell-task{
        vertical-align: top;
        position: relative;
        max-width: 110px !important;
        box-sizing: border-box !important;
    }

    .task{
        background: #efc9d8;
        height: 39px !important;
        padding: 1px;
        outline: 1px solid #efa5bf;
        box-sizing: border-box !important;
    }

    .subtitle-row th:nth-child(odd), .title-row th:nth-child(even){
        background: #efefef;
    }

    .wp-table thead th{
        color: #646464;
        font-weight: 400;
    }

    .wp-filter{
        padding: 40px 0;
    }

    .right-sidebar{
        background: #F9FAFB;
        padding: 30px 25px;
    }

    .right-sidebar h4{
        font-size: 16px;
        font-weight: 600;
        color: #383838;
        text-align: center;
    }

    .right-sidebar{
        border-left: 1px solid #d7d7d7;
    }

    .list-freetasks{
        margin: 25px 0;
    }

    .list-freetasks > div{
        margin-bottom: 15px;
        background: #dbc9ef !important;
        outline: 1px solid #b18dd5;
    }

    /*.t-header{
        display: flex;
        flex-wrap: nowrap;
        text-align: center;
    }

    .subtitle-header{
        display: flex;
        flex-wrap: nowrap;
    }

    .title-header, .subtitle-header > div{
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 7px 0;
    }

    .t-header-column:last-child .title-header{
        border-right: none !important;
    }

    .t-column{
        width: 80px;
    }*/

    /*.t-body > div{
        display: flex;
        flex-wrap: nowrap;
    }

    .t-column{
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    .t-row:first-child{
        border-top: 1px solid #000;
    }

    .t-column{
        text-align: center;
    }

    .t-column:last-child{
        border-right: none;
    }

    .wp-month > div {
        width: 300px;
    }

    .wp-weeks > div {
        width: 75px;
    }*/
</style>
