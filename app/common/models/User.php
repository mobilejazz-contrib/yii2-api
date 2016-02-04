<?php
namespace common\models;

use common\models\base\IdentityUser;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * User model
 *
 * @property string $password password
 */
class User extends IdentityUser
{
    const SCENARIO_LOGIN  = 'login';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    public $password;

    public function getFullName()
    {
        return $this->name . " " . $this->last_name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        switch ($this->role)
        {
            case IdentityUser::ROLE_USER:
                return Yii::t('backend', "User");
                break;
            case IdentityUser::ROLE_ADMIN:
                return Yii::t('backend', "Admin");
                break;
        }
    }

    /**
     * @param bool $role
     * @return array|mixed
     */
    public static function getRoles($role = false)
    {

        $roles = self::roles();
        return $role !== false ? ArrayHelper::getValue($roles, $role) : $roles;
    }

    /**
     * @return array List of roles
     */
    public static function roles()
    {
        return array(
            IdentityUser::ROLE_USER       => Yii::t('backend', "User"),
            IdentityUser::ROLE_ADMIN      => Yii::t('backend', "Admin"),
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'status', 'default', 'value' => self::STATUS_ACTIVE ],
            [ 'status', 'in', 'range' => [ self::STATUS_ACTIVE, self::STATUS_DELETED ] ],

            [ 'email', 'filter', 'filter' => 'trim' ],
            [ 'email', 'email' ],
            [ 'email', 'unique', 'targetClass' => '\common\models\User',
              'message'                        => Yii::t('app', 'This email address has already been taken.'),
            ],

            [ 'password', 'string', 'min' => 6 ],

            [ [ 'email', 'name', 'password' ], 'required', 'on' => self::SCENARIO_CREATE ],
            [ [ 'email', 'password' ], 'required', 'on' => self::SCENARIO_LOGIN ],

            [ [ 'email', 'name', 'last_name', 'role', 'status', 'password', 'picture' ], 'safe' ],

        ];
    }

    public function delete()
    {
        $this->setStatus(IdentityUser::STATUS_DELETED);
    }

    public function scenarios()
    {
        $scenarios                        = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN]  = [ 'email', 'password' ];
        $scenarios[self::SCENARIO_CREATE] = [ 'email', 'password', 'name', 'last_name', 'picture' ];
        $scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];
        return $scenarios;
    }
}
