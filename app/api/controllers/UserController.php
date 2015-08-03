<?php
	namespace api\controllers;

	use api\rest\RestActiveController;
	use yii;
	use common\components\APIErrors;
	use common\models\User;

	class UserController extends RestActiveController
	{
		public $modelClass = 'common\models\User';

		public function actions()
		{
			$actions = parent::actions();
			$actions['create']['scenario'] = User::SCENARIO_CREATE;
			$actions['update']['scenario'] = User::SCENARIO_UPDATE;

			unset($actions['delete']);

			return $actions;
		}

		/**
		 * Checks the privilege of the current user.
		 */
		public function checkAccess($action, $model = null, $params = [])
		{
			if ($action=="update" || $action=="delete")
			{
				$this->checkOwner($model, true);
			}
			else if ($action=="index")
			{
				if (Yii::$app->user->getIdentity()->role!=User::ROLE_ADMIN)
					APIErrors::err(APIErrors::ERROR_FORBIDDEN);
			}

			return true;
		}

		public function actionResetPassword ()
		{
			$post = Yii::$app->getRequest()->post();
			$user = User::findByEmail($post["email"]);


			if (!$user)
				APIErrors::err(APIErrors::ERROR_USER_NOT_FOUND);

			if (!User::isPasswordResetTokenValid ($user->password_reset_token))
			{
				$user->generatePasswordResetToken ();
			}

			if ($user->save ())
			{
				$data = [
					'name' => $user->name,
					'url'  => \Yii::$app->urlManagerFrontEnd->createAbsoluteUrl (['site/reset-password', 'token' => $user->password_reset_token])
				];

				$st = \Yii::$app->mailer
					->compose ('recover-password-' . \Yii::$app->language, $data)
					->setGlobalMergeVars ($data)
					->setFrom ([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
					->setTo ($post['email'])
					->setSubject (\Yii::t ('app', 'Password reset request'))
					->enableAsync ()
					->send ();

				if ($st)
					return ["status"=>true];
			}

			APIErrors::err(APIErrors::ERROR_UNKNOWN);
		}

		public function actionMe ()
		{
			return Yii::$app->user->getIdentity();
		}

	}

