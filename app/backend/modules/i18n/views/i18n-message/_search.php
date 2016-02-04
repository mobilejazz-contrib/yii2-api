<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\i18n\models\search\I18nMessageSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="i18n-message-search">

    <?php $form = ActiveForm::begin([
        'action' => [ 'index' ],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'language') ?>

    <?php echo $form->field($model, 'translation') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', [ 'class' => 'btn btn-primary' ]) ?>
        <?php echo Html::resetButton('Reset', [ 'class' => 'btn btn-primary' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
