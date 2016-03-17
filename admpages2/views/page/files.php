<?php

use pavlinter\admpages2\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use mihaildev\elfinder\Assets;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model pavlinter\admpages2\models\Page */
/* @var $startPath string */
/* @var $id_parent integer */
/* @var $elfinderData array */

Yii::$app->i18n->disableDot();
$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Module::t('', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('', 'Files');
Yii::$app->i18n->resetDot();

Assets::register($this);
Assets::addLangFile(Yii::$app->language, $this);

$this->registerJs('
    var btn = $.fn.button.noConflict();
    $.fn.btn = btn;
    $("#elfinder").elfinder({
        url  : "'. Url::to(ArrayHelper::merge(['/adm/elfinder/connect', 'startPath' => $startPath], $elfinderData)).'",
        lang : "'.Yii::$app->language.'",
        customData: {"'.Yii::$app->request->csrfParam.'":"'.Yii::$app->request->csrfToken.'"},
        rememberLastDir : false,
    });
');
?>
<div class="admpages-files">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('', 'Update'), ['update', 'id' => $model->id, 'id_parent' => $id_parent], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('', 'Delete'), ['delete', 'id' => $model->id, 'id_parent' => $id_parent], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('', 'Are you sure you want to delete this item?', ['dot' => false]),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div id="elfinder"></div>
</div>
