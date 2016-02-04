<?php
use backend\assets\AppAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$bundle = AppAsset::register($this);

$this->params['body-class'] = array_key_exists('body-class', $this->params) ?
    $this->params['body-class']
    : null;
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php echo Html::csrfMetaTags() ?>
        <title><?php echo Html::encode($this->title) ?></title>
        <?php $this->head() ?>

    </head>
    <?php echo Html::beginTag('body',
        [
            'class' => implode(' ',
                [
                    ArrayHelper::getValue($this->params, 'body-class'),
                    'login-page',
                ]),
        ]) ?>
    <?php $this->beginBody() ?>
    <?php echo $content ?>
    <?php $this->endBody() ?>
    <?php echo Html::endTag('body') ?>
    </html>
<?php $this->endPage() ?>