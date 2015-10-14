<?php
	/**
	 * Created by PhpStorm.
	 * User: aleix
	 * Date: 16/9/15
	 * Time: 16:25
	 */
	namespace console\controllers;

	use common\models\Notification;
	use yii\console\Controller;

	class NotifyController extends Controller
	{
		public function actionIndex ()
		{
			$notifications = Notification::find()->where(['sent_at' => null])->all();

			foreach ($notifications as $notification)
			{
				$notification->send ();
			}
		}

	}