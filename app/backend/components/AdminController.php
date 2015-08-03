<?php
/**
 * Created by PhpStorm.
 * User: aleix
 * Date: 1/8/15
 * Time: 9:59
 */

namespace backend\components;


use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends Controller
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'view', 'create', 'update', 'delete'],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'view', 'create', 'update', 'delete'],
						'roles' => ['admin'],
					],
					[
						'allow' => true,
						'actions' => ['index', 'view', 'create', 'update', 'delete'],
						'matchCallback' => function ($rule, $action)
						{
							return $this->checkOwner($rule, $action);
						},
					],
				],
			],
		];
	}

	public function checkOwner ($rule, $action)
	{
		$model = $this->findModel($_GET["id"]);
		if (isset($model->user_id) && $model->user_id == \Yii::$app->user->id)
			return true;

		if (get_class($model)=='common\models\User' && $model->id == \Yii::$app->user->id)
			return true;

		return false;
	}
}