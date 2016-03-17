<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.0.0
 */

namespace pavlinter\admpages2;

/**
 * Class PageAsset
 */
class PageAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/pavlinter/yii2-adm-pages2/admpages2/assets';
    public $css = [

    ];
    public $js = [
        'js/common.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}