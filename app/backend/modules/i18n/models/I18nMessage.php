<?php

namespace backend\modules\i18n\models;

use Yii;

/**
 * This is the model class for table "{{%i18n_message}}".
 *
 * @property integer           $id
 * @property string            $language
 * @property string            $translation
 * @property string            $sourceMessage
 * @property string            $category
 *
 * @property I18nSourceMessage $sourceMessageModel
 */
class I18nMessage extends \yii\db\ActiveRecord
{
    public $category;
    public $sourceMessage;

    public function afterFind()
    {
        $this->sourceMessage = $this->sourceMessageModel ? $this->sourceMessageModel->message : null;
        $this->category      = $this->sourceMessageModel ? $this->sourceMessageModel->category : null;
        return parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'language'      => 'Language',
            'translation'   => 'Translation',
            'sourceMessage' => 'Source Message',
            'category'      => 'Category',
        ];
    }

    public static function countMissingTranslations()
    {
        return I18nMessage::find()->where([ 'language' => Yii::$app->language, 'translation' => null ])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceMessageModel()
    {
        return $this->hasOne(I18nSourceMessage::className(), [ 'id' => 'id' ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'id', 'language' ], 'required' ],
            [ [ 'id' ], 'exist', 'targetClass' => I18nSourceMessage::className(), 'targetAttribute' => 'id' ],
            [ [ 'translation' ], 'string' ],
            [ [ 'language' ], 'string', 'max' => 16 ],
            [ [ 'language' ], 'unique', 'targetAttribute' => [ 'id', 'language' ] ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%i18n_message}}';
    }
}
