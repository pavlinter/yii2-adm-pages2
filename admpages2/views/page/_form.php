<?php

use kartik\checkbox\CheckboxX;
use pavlinter\admpages2\Module;
use pavlinter\buttons\InputButton;
use yii\helpers\ArrayHelper;
use pavlinter\adm\Adm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model pavlinter\admpages2\models\Page */
/* @var $form yii\widgets\ActiveForm */
/* @var $id_parent integer */

$parents = Module::getInstance()->manager->createPageQuery('find')->with(['translations']);
if (!$model->isNewRecord) {
    $parents->where(['!=', 'id' , $model->id]);
}

$parentsData = ArrayHelper::map($parents->all(), 'id', 'name');

?>

<div class="admpage-form">


    <?php $form = Adm::begin('ActiveForm'); ?>

    <?= $form->errorSummary([$model] + $model->getLangModels(), ['class' => 'alert alert-danger']); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'id_parent')->widget(\kartik\widgets\Select2::classname(), [
                'data' => $parentsData,
                'options' => ['placeholder' => Module::t('', 'Select ...', ['dot' => false])],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
        <?= $form->field($model, 'layout')->widget(\kartik\widgets\Select2::classname(), [
            'data' => Module::getInstance()->pageLayouts,
        ]); ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'type')->widget(\kartik\widgets\Select2::classname(), [
                'data' => Module::getInstance()->pageTypes,
            ]); ?>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3">
            <?= $form->field($model, 'date')->widget(\kartik\datetime\DateTimePicker::classname(), [
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'todayBtn' => true,
                    'autoclose' => true,
                    'minuteStep' => 1,
                    'format' => 'yyyy-mm-dd hh:ii:00'
                ]
            ]); ?>
        </div>

    </div>

    <section class="panel adm-langs-panel">
        <header class="panel-heading bg-light">
            <ul class="nav nav-tabs nav-justified text-uc">
                <?php  foreach (Yii::$app->getI18n()->getLanguages() as $id_language => $language) { ?>
                    <li><a href="#lang-<?= $id_language ?>" data-toggle="tab"><?= $language['name'] ?></a></li>
                <?php  }?>
            </ul>
        </header>
        <div class="panel-body">
            <div class="tab-content">
                <?php
                foreach (Yii::$app->getI18n()->getLanguages() as $id_language => $language) {
                    $modelLang = $model->getTranslation($id_language);
                ?>
                    <div class="tab-pane" id="lang-<?= $id_language ?>">

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <?= $form->field($modelLang, '['.$id_language.']name')->textInput(['maxlength' => 100]) ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <?= $form->field($modelLang, '['.$id_language.']title')->textInput(['maxlength' => 80]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <?= $form->field($modelLang, '['.$id_language.']description')->textarea(['maxlength' => 200]) ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <?= $form->field($modelLang, '['.$id_language.']keywords')->textarea(['maxlength' => 250]) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="admpage-alias-cont">
                                    <?= $form->field($modelLang, '['.$id_language.']alias',[
                                        'options' => [
                                            'class' => 'form-group' . ($modelLang->url?' hide':'')
                                        ],
                                        'template' => '{label}<div class="input-group"><div class="input-group-addon"><a href="javascript:void(0);" class="fa fa-link btn-change-to-link"></a></div>{input}</div>{hint}{error}',
                                    ])->textInput(['maxlength' => 200]) ?>


                                    <?= $form->field($modelLang, '['.$id_language.']url',[
                                        'options' => [
                                            'class' => 'form-group' . ($modelLang->url?'':' hide')
                                        ],
                                        'template' => '{label}<div class="input-group"><div class="input-group-addon"><a href="javascript:void(0);" class="fa fa-unlink btn-change-to-alias"></a></div>{input}</div>{hint}{error}',
                                    ])->textInput(['maxlength' => 2000]) ?>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <?= Adm::widget('Redactor',[
                                    'form' => $form,
                                    'model'      => $modelLang,
                                    'attribute'  => '['.$id_language.']short_text'
                                ]) ?>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                            <?= Adm::widget('Redactor',[
                                'form' => $form,
                                'model'      => $modelLang,
                                'attribute'  => '['.$id_language.']text'
                            ]) ?>
                            </div>
                        </div>
                    </div>
                <?php  }?>
            </div>
        </div>
    </section>

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-4">
                <?= $form->field($model, 'weight')->textInput(['maxlength' => 10]) ?>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 form-without-label">
                <?= $form->field($model, 'active', ["template" => "{input}\n{label}\n{hint}\n{error}"])->widget(CheckboxX::classname(), ['pluginOptions'=>['threeState' => false]]); ?>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 form-without-label">
                <?= $form->field($model, 'visible', ["template" => "{input}\n{label}\n{hint}\n{error}"])->widget(CheckboxX::classname(), ['pluginOptions'=>['threeState' => false]]); ?>
            </div>
        </div>

    <div class="form-group">
        <?= InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create', ['dot' => false]) : Adm::t('', 'Update', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'formSelector' => $form,
        ]);?>

        <?php if ($model->isNewRecord) {?>
            <?= InputButton::widget([
                'label' => Adm::t('', 'Create and insert new', ['dot' => false]),
                'options' => ['class' => 'btn btn-primary'],
                'input' => 'adm-redirect',
                'name' => 'redirect',
                'value' => Url::to(['create', 'id_parent' => $id_parent]),
                'formSelector' => $form,
            ]);?>
        <?php }?>

        <?= InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create and list', ['dot' => false]) : Adm::t('', 'Update and list', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'value' => Url::to(['index', 'id_parent' => $id_parent]),
            'formSelector' => $form,
        ]);?>

        <?= InputButton::widget([
            'label' => $model->isNewRecord ? Adm::t('', 'Create and files', ['dot' => false]) : Adm::t('', 'Update and files', ['dot' => false]),
            'options' => ['class' => 'btn btn-primary'],
            'input' => 'adm-redirect',
            'name' => 'redirect',
            'value' => Url::to(['files', 'id' => '{id}', 'id_parent' => $id_parent]),
            'formSelector' => $form,
        ]);?>
    </div>

    <?php Adm::end('ActiveForm'); ?>

</div>