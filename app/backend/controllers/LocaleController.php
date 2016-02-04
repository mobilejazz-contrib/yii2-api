<?php

namespace backend\controllers;

use backend\models\search\LocaleSearch;
use backend\modules\i18n\models\I18nMessage;
use backend\modules\i18n\models\I18nSourceMessage;
use common\models\Locale;
use common\models\User;
use dmstr\bootstrap\Tabs;
use Yii;
use yii\console\controllers\MessageController;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * LocaleController implements the CRUD actions for Locale model.
 */
class LocaleController extends Controller
{
    /**
     * @var boolean whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [ 'admin', 'translator' ],
                    ],
                    [
                        'allow'        => false,
                        'denyCallback' => function ()
                        {
                            Yii::$app->session->setFlash('error', Yii::t('backend', 'Sorry, only Administrators and Translators can edit/create/update Languages.'));
                            return Yii::$app->response->redirect(Yii::$app->homeUrl);
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Checks if a user is Allowed to see this content.
     * @param User $user
     * @return boolean
     */
    public static function isAllowed(User $user)
    {
        return $user->role === User::ROLE_ADMIN;
    }

    /**
     * Creates a new Locale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model          = new Locale;
        $model->default = 0;
        $model->used    = 1;

        try
        {
            if ($model->load($_POST) && $model->save())
            {
                // ========================================
                // 3 - i18n content.
                // ========================================
                // FIRST DUPLICATE ANY STRING ALREADY FOUND.
                /** @var I18nSourceMessage[] $source_messages */
                $source_messages = I18nSourceMessage::find()->all();
                foreach ($source_messages as $sm)
                {
                    try
                    {
                        $m              = new I18nMessage();
                        $m->id          = $sm->id;
                        $m->language    = $model->lang;
                        $m->translation = null;
                        $m->save(false);
                    }
                    catch (\Exception $e)
                    {
                        // nothing to do here.
                    }
                }

                // THEN LOOK FOR NEW STRINGS TO DECLARE.
                //default console commands outputs to STDOUT so this needs to be declared for wep app
                if ( ! defined('STDOUT'))
                {
                    define('STDOUT', fopen('/tmp/stdout', 'w'));
                }

                // Run script to extract and clean up all the yii:t calls
                //extract messages command
                $migration = new MessageController('message', Yii::$app);
                $migration->runAction('extract', [ '@backend/modules/i18n/config/extract.php' ]);
                //extract messages command end

                return $this->redirect(Url::previous());
            }

            elseif ( ! \Yii::$app->request->isPost)
            {
                $model->load($_GET);
            }
        }
        catch
        (\Exception $e)
        {
            $msg = ( isset( $e->errorInfo[2] ) ) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }
        return $this->render('create', [ 'model' => $model ]);
    }

    /**
     * Deletes an existing Locale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string  $lang
     * @return mixed
     */
    public function actionDelete($id, $lang)
    {
        if ($id == 1)
        {
            Yii::$app->getSession()->setFlash('error', Yii::t('backend', 'Attention. The default language can not be deleted.'));
            return $this->redirect(Url::previous());
        }
        try
        {
            $model = $this->findModel($id, $lang);

            // If this is the default and used language, set english as default and used.
            if ($model->isUsed() || $model->isDefault())
            {
                Locale::defaultToEnglish();
            }

            $model->delete();

            $rows = 0;

            $rows += I18nMessage::deleteAll([ 'language' => $model->lang ]);

            Yii::$app->getSession()->setFlash('success', Yii::t('backend', "A total of {rows} translations have been deleted.", [ 'rows' => $rows ]));
        }
        catch (\Exception $e)
        {
            $msg = ( isset( $e->errorInfo[2] ) ) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->setFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        return $this->redirect([ 'index' ]);
    }

    /**
     * Finds the Locale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string  $lang
     * @return Locale the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id, $lang)
    {
        if (( $model = Locale::findOne([ 'id' => $id, 'lang' => $lang ]) ) !== null)
        {
            return $model;
        }
        else
        {
            throw new HttpException(404, Yii::t('backend', 'The requested page does not exist.'));
        }
    }

    /**
     * Lists all Locale models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new LocaleSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index',
            [
                'dataProvider' => $dataProvider,
                'searchModel'  => $searchModel,
            ]);
    }

    /**
     * Updates an existing Locale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string  $lang
     * @return mixed
     */
    public function actionUpdate($id, $lang)
    {
        $model = $this->findModel($id, $lang);

        if ($model->load($_POST) && $model->save())
        {
            // If the model is the default, make sure all other ones are NOT default.
            if ($model->default == 1)
            {
                /** @var Locale[] $locales */
                $locales = Locale::find()->all();

                foreach ($locales as $l)
                {
                    if ($l->id == $id)
                    {
                        continue;
                    }
                    else
                    {
                        $l->default = 0;
                    }
                    $l->save();
                }
            }

            // Check if we need to change the language
            if ( ! $model->isUsed() && $model->lang == Yii::$app->language)
            {
                // GET THE DEFAULT LANGUAGE
                /** @var Locale $default */
                $default = Locale::findOne([ 'default' => true ]);
                return $this->redirect("/admin/site/set-locale?locale=" . $default->lang);
            }
            return $this->redirect(Url::previous());
        }
        else
        {
            return $this->render('update',
                [
                    'model' => $model,
                ]);
        }
    }

    /**
     * Displays a single Locale model.
     * @param integer $id
     * @param string  $lang
     *
     * @return mixed
     */
    public function actionView($id, $lang)
    {
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        return $this->render('view',
            [
                'model' => $this->findModel($id, $lang),
            ]);
    }
}
