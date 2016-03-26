<?php

/* @var $this yii\web\View */
/* @var $model \pavlinter\admpages2\models\Page */

$this->params['breadcrumbs'][] = $model->name;
?>
<div class="adm-pages-layout-page">
    <h1><?= $model->title ?></h1>
    <div><?= $model->text ?></div>
</div>
