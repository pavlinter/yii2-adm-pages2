<?php

use pavlinter\admpages2\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \pavlinter\admpages2\models\Page */

Module::getInstance()->layout = Module::getInstance()->pageLayout;
$this->title = $model->title;
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="adm-pages-layout-page-image">
    <h1><?= $model->title ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?php
                if ($model->image) {
                    echo Html::img($model->image, ['class' => 'img-responsive']);
                }
            ?>
        </div>
        <div class="col-md-6"><?= $model->text() ?></div>
    </div>

</div>

