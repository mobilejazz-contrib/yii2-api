<?php
/**
 * @var $this yii\web\View
 */
use backend\assets\AppAsset;
use backend\controllers\LocaleController;
use backend\controllers\UserController;
use backend\modules\i18n\controllers\I18nMessageController;
use backend\widgets\Menu;
use common\models\Locale;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$bundle = AppAsset::register($this);
$langs  = Locale::getAllKeys();
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
        <header class="main-header">
            <a href="/admin/" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <?php echo Yii::$app->name ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?= Yii::t("app", "Toggle navigation") ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span><?= strtoupper(Locale::getAllLocalesAsMap()[Yii::$app->language]) ?> <i
                                        class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach ($langs as $lang)
                                {
                                    /** @var Locale $current */
                                    $current = Locale::findOne([ 'lang' => $lang ]);
                                    if ($current->isUsed())
                                    {
                                        ?>
                                        <li>
                                            <a href="/admin/site/set-locale?locale=<?= $lang ?>">
                                                Edit in <?= $current->label ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i>
                                <span><?php echo Yii::$app->user->identity->name ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header light-blue">
                                    <i class="fa fa-user fa-4x" style="color: #ffffff;"></i>
                                    <p>
                                        <?php echo Yii::$app->user->identity->name ?>
                                        <small>
                                            <?php echo Yii::t('backend', 'Member since') . ' ' . date("M d, Y",
                                                    Yii::$app->user->identity->created_at); ?>
                                        </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('backend', 'Profile'),
                                            [ '/user/view', "id" => Yii::$app->user->identity->getId() ],
                                            [ 'class' => 'btn btn-primary btn-flat' ]) ?>
                                    </div>
                                    <div class="pull-right">
                                        <?php echo Html::a(Yii::t('backend', 'Logout'),
                                            [ '/site/logout' ],
                                            [ 'class' => 'btn btn-primary btn-flat', 'data-method' => 'post' ]) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <i class="fa fa-user fa-4x" style="color: #ffffff;"></i>
                    </div>
                    <div class="pull-left info">
                        <p><?php echo 'Hello, ' . Yii::$app->user->identity->name ?></p>
                        <a href="<?php echo Url::to([ '/user/view', "id" => Yii::$app->user->identity->getId() ]) ?>">
                            <i class="fa fa-circle text-success"></i>
                            <?php echo Yii::$app->formatter->asDatetime(time()) ?>
                        </a>
                    </div>
                </div>
                <?php
                /** @var \common\models\User $user */
                $user = Yii::$app->user->getIdentity();
                if (Yii::$app->user->isGuest)
                {
                    $menuItems = [
                        [ 'label' => 'Home', 'url' => [ '/site/index' ] ],
                        [ 'label' => 'Login', 'url' => [ '/site/login' ] ],
                    ];
                }
                else
                {
                    $menuItems = [
                        [
                            'label' => Yii::t('backend', 'Home'),
                            'url'   => [ '/' ],
                            'icon'  => '<i class="fa fa-home"></i>',
                        ],
                        [
                            'label'   => Yii::t('backend', 'Users'),
                            'url'     => [ '/user/index' ],
                            'icon'    => '<i class="fa fa-user"></i>',
                            'visible' => UserController::isAllowed($user),
                        ],
                        [
                            'label'   => Yii::t('backend', 'Languages'),
                            'url'     => [ '/locale/index' ],
                            'icon'    => '<i class="fa fa-language" ></i > ',
                            'visible' => LocaleController::isAllowed($user),
                        ],
                        [
                            'label'   => Yii::t('backend', 'Translations'),
                            'url'     => [ '/i18n/i18n-message/index' ],
                            'icon'    => '<i class="fa fa-flag"></i>',
                            'visible' => I18nMessageController::isAllowed($user),
                        ],
                        [
                            'label'   => Yii::t('backend', 'System Information'),
                            'url'     => [ '/system-information/index' ],
                            'icon'    => '<i class="fa fa-line-chart"></i>',
                            'visible' => true,
                        ],
                    ];
                }
                ?>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php echo Menu::widget([
                    'options'         => [ 'class' => 'sidebar-menu' ],
                    'linkTemplate'    => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                    'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                    'activateParents' => true,
                    'items'           => $menuItems,
                ]) ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo $this->title ?>
                    <?php if (isset( $this->params['subtitle'] )): ?>
                        <small><?php echo $this->params['subtitle'] ?></small>
                    <?php endif; ?>
                </h1>

                <?php echo Breadcrumbs::widget([
                    'tag'   => 'ol',
                    'links' => isset( $this->params['breadcrumbs'] ) ? $this->params['breadcrumbs'] : [ ],
                ]) ?>

                <?php if (Yii::$app->session->hasFlash('error'))
                {
                    ?>
                    <div class="alert alert-danger alert-dismissible" style="    margin-top: 20px;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><?= Yii::t('backend', 'Error') ?></h4>
                        <p><?= Yii::$app->session->getFlash('error') ?></p>
                    </div>
                    <?php
                } ?>

            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')): ?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body'    => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ]) ?>
                <?php endif; ?>
                <?php echo $content ?>
            </section>
            <!-- /.content -->
        </aside>
        <!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>