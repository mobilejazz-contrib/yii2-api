<?php

namespace common\behaviors;

use common\models\Locale;
use Yii;
use yii\base\Behavior;
use yii\web\Application;

/**
 * Class LocaleBehavior
 * @package common\behaviors
 */
class LocaleBehavior extends Behavior
{
    /**
     * @var string
     */
    public $cookieName = '_locale';

    /**
     * @var bool
     */
    public $enablePreferredLanguage = true;

    /**
     * Resolve application language by checking user cookies, preferred language and profile settings
     */
    public function beforeRequest()
    {
        if (
            Yii::$app->getRequest()->getCookies()->has($this->cookieName)
            && ! Yii::$app->session->hasFlash('forceUpdateLocale')
        )
        {
            $userLocale = Yii::$app->getRequest()->getCookies()->getValue($this->cookieName);
        }
        else
        {
            $userLocale = Yii::$app->language;
            if ($this->enablePreferredLanguage)
            {
                $userLocale = Yii::$app->request->getPreferredLanguage($this->getAvailableLocales(true));
            }
        }
        if ( ! Locale::isLocaleUsed($userLocale))
        {
            $userLocale = Locale::getDefault()->lang;
        }
        Yii::$app->language = $userLocale;
    }

    /**
     * @param bool $only_used
     * @return array
     */
    protected function getAvailableLocales($only_used = false)
    {
        return array_keys(Locale::getAllLocalesAsMap($only_used));
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest',
        ];
    }
}
