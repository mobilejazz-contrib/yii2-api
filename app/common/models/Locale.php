<?php

namespace common\models;

use common\models\base\Locale as BaseLocale;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "locale".
 */
class Locale extends BaseLocale
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function defaultToEnglish()
    {
        $english          = self::findOne(1);
        $english->default = 1;
        $english->used    = 1;
        $english->save();
    }

    public static function getAllKeys($only_used = false)
    {
        return array_keys(self::getAllLocalesAsMap($only_used));
    }

    public static function getAllLocalesAsMap($only_used = false)
    {
        $tr = array();
        /** @var Locale[] $all */
        $all = Locale::find()->all();
        foreach ($all as $t)
        {
            if ($only_used && ! $t->isUsed())
            {
                continue;
            }
            $tr[$t->lang] = $t->label;
        }

        return $tr;
    }

    /**
     * @return bool true if the language should be used, false otherwise.
     */
    public function isUsed()
    {
        return $this->used;
    }

    public static function getAllLabels()
    {
        $tr = array();
        /** @var Locale[] $all */
        $all = Locale::find()->all();
        foreach ($all as $t)
        {
            $tr[$t->label] = $t->label;
        }

        return $tr;
    }

    public static function getAllLocales()
    {
        $tr = array();
        /** @var Locale[] $all */
        $all = Locale::find()->all();
        foreach ($all as $t)
        {
            $tr[$t->lang] = $t->lang;
        }

        return $tr;
    }

    public static function getUsedLocales()
    {
        $tr = array();
        /** @var Locale[] $all */
        $all = Locale::find()->all();
        foreach ($all as $t)
        {
            if ($t->isUsed())
            {
                $tr[$t->lang] = $t->lang;
            }
        }

        return $tr;
    }

    public static function getCurrentLanguageAsMap()
    {

        return [ Yii::$app->language => self::getCurrent() ];
    }

    public static function getCurrent()
    {
        return self::getAllLocalesAsMap()[Yii::$app->language];
    }

    public static function getDefault()
    {
        return self::findOne([ 'default' => 1 ]);
    }

    public function isDefault()
    {
        return $this->default;
    }

    public static function isLocaleUsed($lang)
    {
        $locale = self::findOne([ 'lang' => $lang ]);
        if (isset( $locale ))
        {
            return $locale->isUsed();
        }
        return false;
    }

    public static function isMultiLanguageSite()
    {
        return count(self::getUsedLocales()) > 1;
    }
}
