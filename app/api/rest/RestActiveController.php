<?php
	/**
	 * Created by PhpStorm.
	 * User: aleix
	 * Date: 31/7/15
	 * Time: 10:54
	 */

	namespace api\rest;

	use common\models\User;
	use yii\filters\AccessControl;
	use yii\rest\ActiveController;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\filters\auth\HttpBearerAuth;
	use mobilejazz\yii2\oauth2server\filters\ErrorToExceptionFilter;
	use mobilejazz\yii2\oauth2server\filters\auth\CompositeAuth;


	class RestActiveController extends ActiveController
	{
		public function behaviors()
		{
			return ArrayHelper::merge(parent::behaviors(), [
				'authenticator' => [
					'class' => CompositeAuth::className(),
					'authMethods' => [
						['class' => HttpBearerAuth::className()],
					]
				],
				'exceptionFilter' => [
					'class' => ErrorToExceptionFilter::className()
				],
				[
					'class' => 'yii\filters\HttpCache',
					'cacheControlHeader' => 'no-cache',
					'lastModified' => function ($action, $params) {
						return time();
					},
				],
			]);
		}

		public function checkOwner ($model, $allowAdmin = false)
		{
			if (isset($model->user_id) && $model->user_id == \Yii::$app->user->id)
				return true;

			if (get_class($model)=='common\models\User' && $model->id == \Yii::$app->user->id)
				return true;

			if ($allowAdmin && Yii::$app->user->getIdentity()->role==User::ROLE_ADMIN)
				return true;

			APIErrors::err(APIErrors::ERROR_FORBIDDEN);
			return false;
		}
	}