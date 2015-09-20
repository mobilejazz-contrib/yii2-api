<?php

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "user_notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $item_class
 * @property integer $item_id
 * @property string $message
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 * @property string $sent_at
 * @property string $read_at
 *
 * @property \common\models\User $user
 */
class UserNotification extends \common\components\TimeStampActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'message'], 'required'],
            [['user_id', 'item_id'], 'integer'],
            [['data'], 'string'],
            [['created_at', 'updated_at', 'sent_at', 'read_at'], 'safe'],
            [['item_class', 'message'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'item_class' => Yii::t('app', 'Item Class'),
            'item_id' => Yii::t('app', 'Item ID'),
            'message' => Yii::t('app', 'Message'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'sent_at' => Yii::t('app', 'Sent At'),
            'read_at' => Yii::t('app', 'Read At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
}
