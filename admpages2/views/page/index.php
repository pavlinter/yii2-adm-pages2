<?php

use kartik\grid\GridView;
use pavlinter\adm\Adm;
use pavlinter\admpages2\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel pavlinter\admpages2\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $id_parent integer */

Yii::$app->i18n->disableDot();
$this->title = Module::t('', 'Pages');
$this->params['breadcrumbs'] = [];
$searchModel::breadcrumbsTree($this->params['breadcrumbs'], $id_parent);
array_unshift($this->params['breadcrumbs'], ['label' => $this->title, 'url' => ['index', 'id_parent' => 0]]);
Yii::$app->i18n->resetDot();
?>
<div class="page-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('', 'Create Page'), ['create', 'id_parent' => $id_parent], ['class' => 'btn btn-primary']) ?>

        <?= Html::a(Module::t('', 'All pages'), [''], ['class' => 'btn btn-primary']) ?>

        <?= Html::a(Module::t('', 'Front pages'), ['','id_parent' => 0], ['class' => 'btn btn-primary']) ?>

        <?= Html::a('!', '#', ['class' => 'btn btn-primary btn-adm-nestable-view' . ($id_parent === false ? ' hide' : '' )]) ?>
    </p>

    <?= Adm::widget('GridView',[
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'nestable' => $id_parent === false ? false : [
            'id' => 'pages-nestable-grid',
            'btn' => false, //hide btn
            'orderBy' => SORT_DESC,
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'id_parent',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'format' => 'html',
                'enableSorting' => false,
                'visible' => $id_parent === false,
                'value' => function ($model) {
                    if ($model->parent) {
                        return Html::a($model->parent->name,['','id_parent' => $model->id_parent]);
                    }
                },
            ],
            [
              'attribute' => 'name',
              'vAlign' => 'middle',
              'hAlign' => 'center',
              'value' => function ($model) {
                  return $model->name;
              },
            ],
            [
                'attribute' => 'title',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'value' => function ($model) {
                    return $model->title;
                },
            ],
            [
                'attribute' => 'alias',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'value' => function ($model) {
                    if ($model->url) {
                        return $model->url;
                    }
                    return $model->alias;
                },
            ],
            [
                'attribute' => 'layout',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=> Module::getInst()->pageLayouts,
                'value' => function ($model) {
                    if (isset(Module::getInst()->pageLayouts[$model->layout])) {
                        return Module::getInst()->pageLayouts[$model->layout];
                    }
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'width' => '100px',
                        'allowClear' => true,
                    ],
                ],
                'filterInputOptions' => ['placeholder' => Module::t('','Select ...', ['dot' => false])],
                'format' => 'raw'
            ],
            [
                'attribute' => 'type',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=> Module::getInst()->pageTypes,
                'value' => function ($model) {
                    if (isset(Module::getInst()->pageTypes[$model->type])) {
                        return Module::getInst()->pageTypes[$model->type];
                    }
                },
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'width' => '100px',
                        'allowClear' => true,
                    ],
                ],
                'filterInputOptions' => ['placeholder' => Module::t('','Select ...', ['dot' => false])],
                'format' => 'raw'
            ],
            [
                'attribute' => 'weight',
                'width' => '50px',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],
            [
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'active',
                'width' => '50px',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['placeholder' => Module::t('','Select ...', ['dot' => false])],
                    'pluginOptions' => [
                        'width' => '100px',
                        'allowClear' => true,
                    ],
                ],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'width' => '130px',
                'template' => '{view} {update} {subpages} {files} {copy} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $url = ['update', 'id' => $model->id];
                        if ($model->id_parent) {
                            $url['id_parent'] = $model->id_parent;
                        } else {
                            $url['id_parent'] = 0;
                        }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        if (in_array($model->id, Module::getInst()->closeDeletePage)) {
                            return null;
                        }
                        $url = ['delete', 'id' => $model->id];
                        if ($model->id_parent) {
                            $url['id_parent'] = $model->id_parent;
                        } else {
                            $url['id_parent'] = 0;
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Module::t('title', 'Delete', ['dot' => false]),
                            'data-confirm' => Module::t('', 'Are you sure you want to delete this item?', ['dot' => false]),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                    'view' => function ($url, $model) {
                        if ($model->alias) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $model->url(), [
                                'title' => Module::t('title', 'View', ['dot' => false]),
                                'data-pjax' => '0',
                                'target' => '_blank'
                            ]);
                        }
                    },
                    'copy' => function ($url, $model) {
                        $url = ['create', 'id' => $model->id];
                        if ($model->id_parent) {
                            $url['id_parent'] = $model->id_parent;
                        } else {
                            $url['id_parent'] = 0;
                        }
                        return Html::a('<span class="fa fa-copy"></span>', $url, [
                            'title' => Module::t('title', 'Copy', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                    'subpages' => function ($url, $model) {
                        return Html::a('<span class="fa fa-plus-circle"></span>', ['', 'id_parent' => $model->id], [
                            'title' => Module::t('title', 'Sub pages', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                    'files' => function ($url, $model) {
                        if (!isset(Module::getInst()->files[$model->type])) {
                            return null;
                        }
                        return Html::a('<span class="fa fa-cloud-download"></span>', ['files', 'id' => $model->id, 'id_parent' => $model->id], [
                            'title' => Module::t('title', 'Files', ['dot' => false]),
                            'data-pjax' => '0',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
