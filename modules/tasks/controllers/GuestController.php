<?php

namespace app\modules\tasks\controllers;

use yii\base\BaseObject;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\tasks\models\TaskForm;

class GuestController extends Controller
{
    public $layout = 'guest.php';

    public function actionCreate($projectId, $companyId)
    {
        $model = new TaskForm();
        $model->setCompany($companyId);
        $model->setProject($projectId);

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->create();

            \Yii::$app->session->setFlash('success', 'Задача успешно добавлена');
        }

        return $this->render('create', ['model' => $model]);
    }
}