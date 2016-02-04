<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title                   = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    <div class="login-logo">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="header"></div>

    <div class="login-box-body">

        <div class="body">
            <?php $form = ActiveForm::begin([ 'id' => 'login-form' ]); ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox([ 'class' => 'simple' ]) ?>
        </div>
        <div class="footer">
            <?php echo Html::submitButton(Yii::t('backend', 'Sign me in'),
                [
                    'class' => 'btn btn-primary btn-flat btn-block',
                    'name'  => 'login-button',
                ]) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
