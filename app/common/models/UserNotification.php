<?php

namespace common\models;

use Yii;
use \common\models\base\UserNotification as BaseUserNotification;

/**
 * This is the model class for table "user_notification".
 */
class UserNotification extends BaseUserNotification
{
    public function send ()
    {
        $data = json_decode($this->data);
        \Yii::$app->pn->send2d ($this->getUser()->one()->username, $this->message, $data);
        $this->sent_at = new Expression('NOW()');
        $this->save();
    }
}
