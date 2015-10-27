<?php
namespace common\models\base;

use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\web\IdentityInterface;

/**
 * User Identitymodel
 *
 * @property string $password password
 */
class IdentityUser extends \common\models\base\User implements IdentityInterface, UserCredentialsInterface
{
	const ROLE_USER = 10;
	const ROLE_ADMIN = 20;

	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 10;

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
		parent::afterSave($insert, $changedAttributes);

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

		//Check if we have a UserProfile, we create one if it doesn't exists
		$profile = UserProfile::findOne($this->id);
		if (!isset($profile))
		{
			$profile = new UserProfile();
			$profile->id = $this->id;
			$profile->save();
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
		if ($this->password_hash=="#")
			return false;

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

	public static function status()
	{
		return array(
			IdentityUser::STATUS_DELETED => Yii::t('app', "Deleted"),
			IdentityUser::STATUS_ACTIVE => Yii::t('app', "Active")
		);
	}

	public function getStatus()
	{
		switch ($this->status)
		{
			case IdentityUser::STATUS_DELETED:
				return Yii::t('app', "Deleted");
				break;
			case IdentityUser::STATUS_ACTIVE:
				return Yii::t('app', "Active");
				break;
		}
	}
}
