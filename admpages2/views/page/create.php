<?php

use pavlinter\admpages2\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model pavlinter\admpages2\models\Page */
/* @var $id_parent integer */

Yii::$app->i18n->disableDot();
$this->title = Module::t('', 'Create Page');
$this->params['breadcrumbs'] = [];
$model::breadcrumbsTree($this->params['breadcrumbs'], $id_parent, ['lastLink' => true]);
array_unshift($this->params['breadcrumbs'], ['label' => Module::t('', 'Pages'), 'url' => ['index', 'id_parent' => 0]]);
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->i18n->resetDot();
?>
<div class="page-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id_parent' => $id_parent,
    ]) ?>

</div>
