<?php

namespace app\modules\tasks\controllers;

use app\models\bitrix\Bitrix;
use app\modules\tasks\models\CommentForm;
use app\modules\tasks\models\FilterForm;
use app\modules\tasks\models\Project;
use app\modules\tasks\models\Task;
use app\modules\tasks\models\TaskComment;
use app\modules\tasks\models\TaskForm;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\helpers\Url;
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
                        'actions' => ['index', 'task', 'create', 'edit', 'update-comment', 'delete-comment', 'projects'],
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
        $model = FilterForm::generate(\Yii::$app->user->identity->company->id);

        return $this->render('index', ['model' => $model]);
    }

    public function actionTask($id)
    {
        $task = Task::findById($id);

        if($task)
        {
            $model = new CommentForm();
            $comments = TaskComment::getList($id);

            if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
            {
                $model->setTaskId($id);
                $model->sendCommentByUser(\Yii::$app->user->identity);

                return $this->refresh();
            }

            return $this->render('task', ['task' => $task, 'model' => $model, 'comments' => $comments]);
        }

        return $this->redirect('/');
    }

    public function actionUpdateComment($id)
    {
        $model = new CommentForm();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->setTaskId($id);
            $model->update();
        }

        return 200;
    }

    public function actionDeleteComment($id)
    {
        $model = new CommentForm();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->setTaskId($id);
            $model->deleteComment();
        }

        return 200;
    }

    public function actionCreate()
    {
        $model = new TaskForm();
        $model->setCompany(\Yii::$app->user->identity->company->id);
        $model->setContact(\Yii::$app->user->identity->id);

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $taskId = $model->create();

            return $this->redirect(Url::to(['task', 'id' => $taskId]));
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionEdit($id)
    {
        $task = Task::findById($id);

        if($task && $task->getCompanyId() == \Yii::$app->user->identity->company->id)
        {
            $model = new TaskForm();
            $model->setCompany(\Yii::$app->user->identity->company->id);
            $model->setContact(\Yii::$app->user->identity->id);
            $model->setRelationTask($task);

            if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
            {
                $model->update();

                return $this->redirect(Url::to(['task', 'id' => $model->id]));
            }

            return $this->render('edit', ['model' => $model]);
        }

        return $this->redirect('/');
    }

    public function actionProjects()
    {
        $projects = Project::getProjectByCompany(\Yii::$app->user->identity->company->title);

        return $this->render('projects', ['projects' => $projects]);
    }
}
