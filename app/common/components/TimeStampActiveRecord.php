<?php
/**
 * Created by PhpStorm.
 * User: aleix
 * Date: 31/7/15
 * Time: 18:43
 */

namespace common\components;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii;

class TimeStampActiveRecord extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public function behaviors ()
	{
		return [
			'timestamp' => [
				'class'      => TimestampBehavior::className (),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			],
		];
	}

	public function fields ()
	{
		$fields = parent::fields ();

		// remove updated_at field
		unset($fields['updated_at']);

		return $fields;
	}
}