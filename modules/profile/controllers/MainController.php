<?php

namespace app\modules\profile\controllers;

use app\modules\profile\models\ContactForm;
use app\modules\profile\models\Product;
use app\modules\profile\models\Requisite;
use yii\filters\AccessControl;
use yii\web\Controller;

class MainController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return $action->controller->redirect('/login');
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $model = ContactForm::instanceByUser(\Yii::$app->user->identity->id);

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->updateByUser(\Yii::$app->user->identity->id);
        }

        return $this->render('index', ['model' => $model]);
    }
}
