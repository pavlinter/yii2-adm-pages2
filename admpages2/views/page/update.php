<?php

use pavlinter\admpages2\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model pavlinter\admpages2\models\Page */
/* @var $id_parent integer */

Yii::$app->i18n->disableDot();
$this->title = Module::t('', 'Update Page: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('', 'Update');
Yii::$app->i18n->resetDot();
?>
<div class="page-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'id_parent' => $id_parent,
    ]) ?>

</div>
