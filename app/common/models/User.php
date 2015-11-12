<?php
namespace common\models;

use common\components\TimeStampActiveRecord;
use common\models\base\IdentityUser;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $password password
 */
class User extends \common\models\base\User
{
	public $password;

	const SCENARIO_LOGIN = 'login';
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_LOGIN] = ['email', 'password'];
		$scenarios[self::SCENARIO_CREATE] = ['email', 'password', 'name', 'last_name', 'picture'];
		$scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];
		return $scenarios;
	}

	public function rules ()
	{
		$rules = parent::rules ();

		$rules_new = [
				['status', 'default', 'value' => self::STATUS_ACTIVE],
				['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

				['email', 'filter', 'filter' => 'trim'],
				['email', 'email'],

				['email',
				 'unique',
				 'targetClass' => '\common\models\User',
				 'message'     => Yii::t ('app', 'This email address has already been taken.')
				],
				[
						'username',
						'unique',
						'targetClass' => '\common\models\User',
						'message'     => Yii::t ('app', 'This username has already been taken.')
				],

				['password', 'string', 'min' => 6],

				[['username', 'password'], 'required', 'on' => self::SCENARIO_CREATE],
				[['username', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],

				[['email', 'name', 'last_name', 'role', 'status', 'password', 'picture'], 'safe'],
		];


		return array_merge ($rules, $rules_new);
	}


	/**
	 * @return List of roles
	 */
	public static function roles()
	{
		return array(
			IdentityUser::ROLE_USER => Yii::t('app', "User"),
			IdentityUser::ROLE_ADMIN => Yii::t('app', "Admin"),
		);
	}

	public function getRole()
	{
		switch ($this->role)
		{
			case IdentityUser::ROLE_USER:
				return Yii::t('app', "User");
				break;
			case IdentityUser::ROLE_ADMIN:
				return Yii::t('app', "Admin");
				break;
		}
	}

	public function getFullName ()
	{
		return $this->name . " " . $this->last_name;
	}
}
