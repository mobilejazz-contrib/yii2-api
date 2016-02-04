<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('backend', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <!-- Quick tools -->
        <div class="form-inline">
            <div class="form-group">
                <?= Html::a('<i class="fa fa-plus icon-margin small"></i> ' . Yii::t('backend', 'Add New'),
                    [ 'create' ],
                    [ 'class' => 'btn btn-primary' ]) ?>
            </div>

            <div class="form-group">
                <form>
                    <!-- Bulk actions -->
                    <?= Html::dropDownList('bulk-action',
                        null,
                        [ ''           => Yii::t('backend', 'Bulk Actions'),
                          'delete'     => Yii::t('backend', 'Delete'),
                          'activate'   => Yii::t('backend', 'Activate'),
                          'deactivate' => Yii::t('backend', 'Deactivate'),
                        ],
                        [
                            'id'    => 'bulk-dropdown',
                            'class' => 'form-control',
                        ]) ?>
                    <?= Html::button(
                        "<i class=\"fa fa-check icon-margin small\"></i> " . Yii::t("backend", "Apply"),
                        [
                            'id'    => 'bulk-action-submit',
                            'class' => 'btn btn-default',
                        ]
                    ) ?>
                </form>
            </div>

            <!-- SEARCH -->
            <div class="form-group">
                <?= Html::textInput('search',
                    null,
                    [
                        'id'          => 'user-text-box',
                        'class'       => 'form-control',
                        'placeholder' => Yii::t('backend', 'Search'),
                    ]) ?>
                <?= Html::button(
                    "<i class=\"fa fa-search icon-margin small\"></i> " . Yii::t('backend', 'Search'),
                    [
                        'id'    => 'user-search-btn',
                        'class' => 'btn btn-default',
                    ]
                ) ?>
            </div>
        </div>

        <!-- Spacer -->
        <hr>

        <!-- Main -->
        <?= GridView::widget([
            'layout'       => '{pager}{items}{pager}',
            'tableOptions' => [ 'class' => 'table table-striped' ],
            'pager'        => [
                'class'   => \backend\widgets\LinkPager::className(),
                'options' => [
                    'class' => 'pagination',
                    'style' => 'display: inline',
                ],
            ],
            'dataProvider' => $dataProvider,
            'columns'      => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                ],
                [
                    'label'  => Yii::t('backend', 'Username'),
                    'value'  => function ($model)
                    {
                        /** @var \common\models\User $model */
                        return Html::a($model->name,
                            [
                                'user/update/?id=' . $model->id,
                            ]);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'role',
                    'label'     => Yii::t('backend', 'Role'),
                    'value'     => function ($model)
                    {
                        /** @var User $model */
                        return Html::a($model->getRole(),
                            [ 'user/index/?UserSearch[role]=' . $model->role ]);
                    },
                    'filter'    => false,
                    'format'    => 'html',
                ],
                [
                    'attribute' => 'status',
                    'value'     => function ($model)
                    {
                        return $model->getStatus();
                    },
                ],
                [
                    'label' => Yii::t('backend', 'Name'),
                    'value' => function ($model)
                    {
                        /** @var User $model */
                        return $model->name . " " . $model->last_name;
                    },
                ],
                [
                    'attribute' => 'email',
                    'filter'    => false,
                ],
                [
                    'class'          => 'yii\grid\ActionColumn',
                    'header'         => Yii::t('backend', 'Action'),
                    'template'       => '{update} | {delete}',
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
                            return Html::a('Edit', $url);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            return Html::a('Delete',
                                $url,
                                [
                                    'data' => [
                                        'confirm' => Yii::t('backend', 'Are you sure you want to delete this menu?'),
                                        'method'  => 'post',
                                    ],
                                ]);
                        },
                    ],
                    'contentOptions' => [ 'nowrap' => 'nowrap' ],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php
$script = <<< JS
// USER SEARCH BY  NAME.
$('#user-text-box').keypress(function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        var text = $('#user-text-box').val();
        window.location.href = "?UserSearch[name]=" + text;
    }
});
$('#user-search-btn').click(function () {
    var text = $('#user-text-box').val();
    window.location.href = "?UserSearch[name]=" + text;
});
JS;
$this->registerJs($script);
?>

