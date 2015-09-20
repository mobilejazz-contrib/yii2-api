<?php

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "user_profile".
 *
 * @property integer $id
 * @property string $about
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \common\models\User $id0
 */
class UserProfile extends \common\components\TimeStampActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['about'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'about' => Yii::t('app', 'About'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'id']);
    }
}
