<?php

/* @var $this yii\web\View */
use backend\widgets\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $searchModel backend\modules\i18n\models\search\I18nMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Translations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="form-inline">
            <div class="form-group">
                <?= Html::a('<i class="fa fa-refresh icon-margin small"></i> ' . Yii::t('backend', 'Scan for new messages'),
                    [ 'scan-for-new-messages' ],
                    [
                        'class'        => 'btn btn-primary',
                        'data-confirm' => Yii::t('backend', 'This will take a while, please wait until the process has ended.'),
                    ]) ?>
            </div>
        </div>
        <hr>
        <?php echo GridView::widget([
            'layout'       => '{pager}{items}{pager}',
            'tableOptions' => [ 'class' => 'table table-striped' ],
            'dataProvider' => $dataProvider,
            'pager'        => [
                'class'   => LinkPager::className(),
                'options' => [
                    'class' => 'pagination',
                    'style' => 'display: inline',
                ],
            ],
            'filterModel'  => $searchModel,
            'columns'      => [
                // 'language',
                // TODO (Pol) RE-ENABLE IF WE WANT TO EDIT DIFFERENT CATEGORIES OTHER THAN MED. ALSO REMEMBER TO RE-ENABLE IN THE SEARCH MODEL THE OTHER CATEGORIES.
                [
                    'attribute' => 'category',
                    'filter'    => $categories,
                ],
                'sourceMessage:ntext',
                'translation:ntext',
                [
                    'class'          => 'yii\grid\ActionColumn',
                    'header'         => Yii::t('backend', 'Edit'),
                    'template'       => '{update}',
                    'urlCreator'     => function ($action, $model, $key, $index)
                    {
                        // using the column name as key, not mapping to 'id' like the standard generator
                        $params    = is_array($key) ? $key : [ $model->primaryKey()[0] => (string) $key ];
                        $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                        return Url::toRoute($params);
                    },
                    'buttons'        => [
                        'update' => function ($url, $model, $key)
                        {
                            return Html::a('Edit',
                                FALSE,
                                [
                                    'class'      => 'showModalButton',
                                    'data-value' => $url,
                                    'label'      => Yii::t('backend', 'Translate String'),
                                    'style'      => 'cursor: pointer;',
                                ]);
                        },
                    ],
                    'contentOptions' => [ 'nowrap' => 'nowrap' ],
                ],
            ],
        ]); ?>
    </div>
</div>