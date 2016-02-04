<?php

use backend\widgets\Submitter;
use common\models\User;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput([ 'maxlength' => true ]) ?>

    <?= $form->field($model, 'last_name')->textInput([ 'maxlength' => true ]) ?>

    <?= $form->field($model, 'email')->textInput([ 'maxlength' => true ]) ?>

    <?= $form->field($model, 'password')->passwordInput([ 'maxlength' => true ]) ?>

    <?= $form->field($model, 'role')->dropDownList(User::roles()) ?>

    <?= $form->field($model, 'status')->dropDownList(User::status()) ?>

    <?= Submitter::widget([
        'model'     => $model,
        'returnUrl' => '/admin/user',
    ]) ?>
    <?php ActiveForm::end(); ?>

</div>
