<?php

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "locale".
 *
 * @property integer $id
 * @property string  $lang
 * @property string  $label
 * @property integer $default
 * @property integer $used
 * @property integer $rtl
 * @property integer $created_at
 * @property integer $updated_at
 */
class Locale extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('backend', 'ID'),
            'lang'       => Yii::t('backend', 'Lang'),
            'label'      => Yii::t('backend', 'Label'),
            'default'    => Yii::t('backend', 'Default'),
            'used'       => Yii::t('backend', 'Used'),
            'rtl'        => Yii::t('backend', 'Right to Left'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'lang', 'label' ], 'required' ],
            [ [ 'default', 'used', 'created_at', 'updated_at' ], 'integer' ],
            [ [ 'used', 'rtl' ], 'default', 'value' => 0 ],
            [ [ 'lang' ], 'string', 'max' => 2 ],
            [ [ 'label' ], 'string', 'max' => 255 ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locale';
    }

}
