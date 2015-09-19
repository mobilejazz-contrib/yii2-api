<?php
namespace common\models;

use common\components\TimeStampActiveRecord;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $name
 * @property string $last_name
 * @property string $role
 * @property integer $status
 * @property string $picture
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password password
 */
class User extends TimeStampActiveRecord implements IdentityInterface, UserCredentialsInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

	const ROLE_USER = 10;
	const ROLE_ADMIN = 20;

	public $password;

	const SCENARIO_LOGIN = 'login';
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

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

	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert))
		{
			if ($this->isNewRecord)
			{
				$this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
				$this->generateAuthKey();
			}
			else if ($this->password)
			{
				$this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	public function afterSave( $insert, $changedAttributes )
	{
		$auth = Yii::$app->authManager;

		switch($this->role)
		{
			case User::ROLE_ADMIN:
				$role = $auth->getRole('admin');
				if (!$role)
				{
					$role = $auth->createRole ('admin');
					$auth->add($role);
				}
				$auth->revokeAll($this->id);
				$auth->assign($role, $this->id);
				break;
			case User::ROLE_USER:
				$role = $auth->getRole('user');
				if (!$role)
				{
					$role = $auth->createRole ('user');
					$auth->add($role);
				}
				$auth->revokeAll($this->id);
				$auth->assign($role, $this->id);
				break;
		}
	}


	public function fields ()
	{
		$fields = parent::fields();
		unset($fields['password_hash'],$fields['password_reset_token'],$fields['auth_key'],$fields['status']);

		return $fields;
	}

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
		$user = \mobilejazz\yii2\oauth2server\models\OauthAccessTokens::find()->where(["access_token"=>$token])->one();
		if ($user)
		{
			//If we have client_credentials enabled, we need to return a fake user to allow access
			if (isset($user->client_id) && $user->user_id==null)
			{
				return new User();
			}
			return static::findOne (["id" => $user->user_id]);
		}

		return null;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

	/**
	 * Required by OAuth2\Storage\UserCredentialsInterfaces
	 *
	 * @param mixed $username
	 * @param mixed $password
	 * @param boolean $updating_password
	 * @return bool whether credentials are valid
	 */
	public function checkUserCredentials($username, $password, $updating_password = false)
	{
		$user = $this->findByEmail($username);
		if(is_null($user))
		{
			return false;
		}

		$crypted = $user->password_hash;

		if(!Yii::$app->getSecurity()->validatePassword($password,$crypted))
		{
			return false;
		}
		else
			return true;
	}

	/**
	 * Required by OAuth2\Storage\UserCredentialsInterfaces
	 *
	 * @param string $username
	 * @return array with keys scope and user_id
	 */
	public function getUserDetails($username)
	{
		$user = $this->findByEmail($username);
		return ['scope'=>'','user_id'=>$user->id];
	}


	/**
	 * @return Item map to be used in DropDowns or Lists
	 */
	public static function getDataList($roles = NULL)
	{
		if (count($roles)>0)
		{
			$query = User::find();
			foreach ($roles as $i => $role)
			{
				if ($i==0)
					$query->where(['role'=>$role]);
				else
					$query->orWhere(['role'=>$role]);
			}
		}
		else
		{
			$query = User::find();
		}


		$models = $query->orderBy('email')->asArray()->all();

		return ArrayHelper::map($models, 'id', 'email');
	}

	/**
	 * @return A list of roles
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

	public static function status()
	{
		return array(
			User::STATUS_DELETED => Yii::t('app', "Deleted"),
			User::STATUS_ACTIVE => Yii::t('app', "Active")
		);
	}

	public function getStatus()
	{
		switch ($this->status)
		{
			case User::STATUS_DELETED:
				return Yii::t('app', "Deleted");
				break;
			case User::STATUS_ACTIVE:
				return Yii::t('app', "Active");
				break;
		}
	}

	public function getFullName ()
	{
		return $this->name . " " . $this->last_name;
	}
}