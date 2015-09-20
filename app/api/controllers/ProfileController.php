<?php
	namespace api\controllers;

	use api\rest\RestActiveController;
	use yii;
	use common\components\APIErrors;
	use common\models\User;

	class ProfileController extends RestActiveController
	{
		public $modelClass = 'common\models\UserProfile';

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
	}

