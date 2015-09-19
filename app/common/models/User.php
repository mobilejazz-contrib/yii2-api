<?php
namespace common\models;

use common\components\TimeStampActiveRecord;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property string $password password
 */
class User extends \common\models\base\IdentityUser
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

			['email', 'filter', 'filter' => 'trim'],
			['email', 'email'],
			['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','This email address has already been taken.')],

			['password', 'string', 'min' => 6],

			[['email', 'name', 'password'], 'required', 'on' => self::SCENARIO_CREATE ],
			[['email', 'password'], 'required', 'on' => self::SCENARIO_LOGIN ],


			[['email', 'name', 'last_name', 'role', 'status', 'password', 'picture'], 'safe'],

		];
    }


	/**
	 * @return List of roles
	 */
	public static function roles()
	{
		return array(
			User::ROLE_USER => Yii::t('app', "User"),
			User::ROLE_ADMIN => Yii::t('app', "Admin"),
		);
	}

	public function getRole()
	{
		switch ($this->role)
		{
			case User::ROLE_USER:
				return Yii::t('app', "User");
				break;
			case User::ROLE_ADMIN:
				return Yii::t('app', "Admin");
				break;
		}
	}

	public function getFullName ()
	{
		return $this->name . " " . $this->last_name;
	}
}
