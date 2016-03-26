<?php

use pavlinter\admpages2\Module;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admpages2\models\Page */

Module::getInst()->layout = Module::getInstance()->pageLayout;
$this->title = $model->title;
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="adm-pages-layout-page">
    <h1><?= $model->title ?></h1>
    <div><?= $model->text ?></div>
</div>
