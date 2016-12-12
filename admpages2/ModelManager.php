<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.1
 */

namespace pavlinter\admpages2;

use pavlinter\adm\Manager;
use Yii;

/**
 * @method \pavlinter\admpages2\models\Page staticPage
 * @method \pavlinter\admpages2\models\Page createPage
 * @method \pavlinter\admpages2\models\Page createPageQuery
 * @method \pavlinter\admpages2\models\PageSearch createPageSearch
 * @method \pavlinter\admpages2\models\PageLang staticPageLang
 * @method \pavlinter\admpages2\models\PageLang createPageLang
 * @method \pavlinter\admpages2\models\PageLang createPageLangQuery
 */
class ModelManager extends Manager
{
    /**
     * @var string|\pavlinter\admpages2\models\Page
     */
    public $pageClass = 'pavlinter\admpages2\models\Page';
    /**
     * @var string|\pavlinter\admpages2\models\PageSearch
     */
    public $pageSearchClass = 'pavlinter\admpages2\models\PageSearch';
    /**
     * @var string|\pavlinter\admpages2\models\PageLang
     */
    public $pageLangClass = 'pavlinter\admpages2\models\PageLang';
}