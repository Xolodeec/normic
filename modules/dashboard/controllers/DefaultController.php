<?php

namespace app\modules\dashboard\controllers;

use app\modules\dashboard\models\Dashboard;
use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = 'main.php';
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $model = new Dashboard();

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $model->create();
        }

        return $this->render('index', ['model' => $model]);
    }
    public function actionInstall()
    {
        return $this->render('install');
    }
	
	public function actionTest()
    {
        $body = '{
		"token": "hKJJRAGkbmFtZb/QkNC00LzQuNC90LjRgdGC0YDQsNGC0L7RgCDQotCapWxvZ2luoTGhdMtCeJ5TLT1AAA==:Lo52WzCROY0YvRin7zDTnQ",
		"dbId": "GLOBAL",
  		"method": "query",
  		"args": [
    		{
      			"path": "bitrix/getPatientData",
      			"params": { "date": "2023-08-20 12:00:00" }
    		}
  		],
  		"pk": false
		}';
		
		$ch = curl_init();
		
      curl_setopt($ch, CURLOPT_URL, 'https://oris.medwork.ru:55555/db/json');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSLCERT, "/var/www/pa.normik.ru/web/oris.pem");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));

      $result = curl_exec($ch);
      curl_close($ch);
	        echo "<pre>";
      var_dump($result);
      die;
      $result = json_decode($result);

    }

}
