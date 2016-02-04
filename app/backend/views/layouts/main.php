<?php
use yii\bootstrap\Modal;

/**
 * @var $this yii\web\View
 */
$this->beginContent('@backend/views/layouts/common.php');
echo $content;
Modal::begin([
    'options'       => [
        'id'       => 'modal',
        'tabindex' => FALSE,
        'class'    => '',
    ],
    'headerOptions' => [ 'id' => 'modalHeader' ],
    'clientOptions' => [
        'backdrop' => 'true',
        'keyboard' => TRUE,
    ],
]);
echo "<div id='modalContent'>
            <div style='text-align:center; font-size: 33px;'>
                <i class='fa fa-refresh fa-spin'></i> " . Yii::t('backend', 'Loading...') . "
            </div>
      </div>";
Modal::end();
$this->endContent(); ?>
