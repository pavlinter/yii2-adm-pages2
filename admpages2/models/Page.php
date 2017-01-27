<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.2
 */

namespace pavlinter\admpages2\models;

use pavlinter\admpages2\Module;
use Yii;
use pavlinter\translation\TranslationBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%page}}".
 *
 * @method \pavlinter\translation\TranslationBehavior getLangModels
 * @method \pavlinter\translation\TranslationBehavior setLanguage
 * @method \pavlinter\translation\TranslationBehavior getLanguage
 * @method \pavlinter\translation\TranslationBehavior saveTranslation
 * @method \pavlinter\translation\TranslationBehavior saveAllTranslation
 * @method \pavlinter\translation\TranslationBehavior saveAll
 * @method \pavlinter\translation\TranslationBehavior validateAll
 * @method \pavlinter\translation\TranslationBehavior validateLangs
 * @method \pavlinter\translation\TranslationBehavior loadAll
 * @method \pavlinter\translation\TranslationBehavior loadLang
 * @method \pavlinter\translation\TranslationBehavior loadLangs
 * @method \pavlinter\translation\TranslationBehavior loadTranslations
 * @method \pavlinter\translation\TranslationBehavior getOneTranslation
 * @method \pavlinter\translation\TranslationBehavior hasTranslation
 *
 * @property string $id
 * @property string $id_parent
 * @property string $layout
 * @property string $type
 * @property string $weight
 * @property integer $visible
 * @property integer $active
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 *
 * Translation
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $alias
 * @property string $url
 * @property string $short_text
 * @property string $text
 *
 * @property PageLang[] $translation
 * @property Page $parent
 * @property Page[] $childs
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'trans' => [
                'class' => TranslationBehavior::className(),
                'translationAttributes' => [
                    'name',
                    'title',
                    'description',
                    'alias',
                    'url',
                    'short_text',
                    'text',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $module = Module::getInst();
        return [
            [['weight', 'id_parent'], 'default', 'value' => null],
            [['id_parent', 'weight', 'visible', 'active'], 'integer'],
            [['layout', 'type'], 'required'],
            [['layout', 'type'], 'string', 'max' => 50],
            [['date'], 'date', 'format' => 'yyyy-MM-dd HH:mm:ss', 'isEmpty' => function ($value) {
                if(!$value){
                    $this->date = date('Y-m-d H:i:00');
                }
                return false;
            }],
            [['layout'], 'in', 'range' => array_keys($module->pageLayouts)],
            [['type'], 'in', 'range' => array_keys($module->pageTypes)],
        ];
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modelAdm/admpages', 'ID'),
            'id_parent' => Yii::t('modelAdm/admpages', 'Parent'),
            'layout' => Yii::t('modelAdm/admpages', 'Layout'),
            'weight' => Yii::t('modelAdm/admpages', 'Weight'),
            'visible' => Yii::t('modelAdm/admpages', 'Visible'),
            'active' => Yii::t('modelAdm/admpages', 'Active'),
            'date' => Yii::t('modelAdm/admpages', 'Date'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->weight === null) {
            $query = static::find()->select(['MAX(weight)']);
            if (!$insert) {
                $query->where(['!=', 'id', $this->id]);
            }
            $this->weight = $query->scalar() + 50;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param $url
     * @param null $id_language
     * @param string $key
     * @return mixed
     */
    public function url($url = true, $id_language = null, $key = 'alias')
    {
        if ($url === true) {
            $url = ['/admpages/default/index'];
        } else if($url === null) {
            $url = ['/admpages/default/main'];
            $key = false;
        }
        return $this->getOneTranslation($id_language)->url($url, $key);
    }

    /**
     * @param bool $scheme
     * @param array $options
     * @return string
     */
    public function urlTo($scheme = false, $options = [])
    {
        $options  = ArrayHelper::merge([
            'url' => true,
            'id_language' => null,
            'key' => 'alias',
            'params' => [],
        ], $options);

        $url = ArrayHelper::merge($this->url($options['url'], $options['id_language'], $options['key']), $options['params']);
        return \yii\helpers\Url::to($url, $scheme);
    }

    /**
     * @param $id
     * @param array $config
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public static function getPage($id, $config = [])
    {
        $config = ArrayHelper::merge([
            'where' => false,
            'orderBy' => false,
        ], $config);

        $query = static::find()->from(['p' => static::tableName()])->innerJoinWith(['translation']);
        if ($config['where'] === false) {
            $query->where(['p.id' => $id]);
        } else {
            $query->where($config['where']);
        }
        if ($config['orderBy'] !== false) {
            $query->orderBy($config['orderBy']);
        }
        /* @var $model self */
        $model = $query->one();

        if (!$model) {
            throw new \yii\web\NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if (!$model->active || !$model->translation) {
            return false;
        }

        static::registerSeo($model, $config);

        return $model;
    }

    /**
     * @param self
     * @param array $config
     */
    public static function registerSeo($model, $config = [])
    {
        /* @var $model self */
        $config = ArrayHelper::merge([
            'setLanguageUrl' => true,
            'registerMetaTag' => true,
            'registerTitle' => true,
        ], $config);

        if ($config['setLanguageUrl']) {
            if (!isset($config['url'])) {
                $url = [''];
            } else {
                $url = $config['url'];
            }

            $languages = Yii::$app->getI18n()->getLanguages();
            foreach ($languages as $id_language => $language) {
                if (is_array($url)) {
                    $language['url'] = ArrayHelper::merge($url, [
                        'lang' => $language[Yii::$app->getI18n()->langColCode],
                    ]);
                    $language['url'] = Yii::$app->getUrlManager()->createUrl($language['url']);
                } elseif (is_callable($url)) {
                    $language['url'] = call_user_func($url, $model, $id_language, $language);
                }

                $href = Url::to($language['url'], true);
                if ($model->type == 'main') {
                    if (Yii::$app->i18n->getId() != $id_language) {
                        Yii::$app->getView()->registerLinkTag([
                            'rel' => 'alternate',
                            'hreflang' => $language[Yii::$app->getI18n()->langColCode],
                            'href' => $href,
                        ]);
                    }
                } else {
                    if (Yii::$app->i18n->getId() != $id_language) {
                        Yii::$app->getView()->registerLinkTag([
                            'rel' => 'alternate',
                            'hreflang' => $language[Yii::$app->getI18n()->langColCode],
                            'href' => $href,
                        ]);
                    }
                }
                Yii::$app->getI18n()->setLanguage($id_language, $language);
            }
        }
        if ($config['registerMetaTag']) {
            Yii::$app->getView()->registerMetaTag(['name' => 'description', 'content' => $model->description]);
        }
        if ($config['registerTitle']) {
            Yii::$app->getView()->title = $model->title;
        }
    }

    /**
     * @param null $id
     * @return mixed
     */
    public static function currentPage($id = null)
    {
        if($id){
            Module::$modelPage = Module::getInst()->manager->createPageQuery('getPage', $id);
        }
        return Module::$modelPage;
    }

    /**
     * @param $layout
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public static function urlLayout($layout, $options = [])
    {
        $module = Module::getInst();

        $options  = ArrayHelper::merge([
            'url' => true,
            'key' => 'alias',
            'params' => [],
        ], $options);

        $url = ['/admpages/default/index'];
        if ($options['url'] !== true) {
            $url = $options['url'];
        }
        if ($module::$layoutAliases === null) {
            $layouts = Yii::$app->cache->get('admpagesUrlLayout' . Yii::$app->language);
            if ($layouts === false) {
                $layouts =  static::find()->from(['p' => static::tableName()])->select(['l.alias', 'p.layout'])
                    ->innerJoin(['l'=> PageLang::tableName()],'l.page_id=p.id AND l.language_id=:language_id',[':language_id' => Yii::$app->getI18n()->getId()])
                    ->where(['p.active' => 1])->groupBy('p.layout')->all();
                $layouts = ArrayHelper::map($layouts, 'layout', 'alias');
                $dependency = new \yii\caching\DbDependency([
                    'sql' => 'SELECT MAX(updated_at) FROM ' . static::tableName(),
                ]);
                Yii::$app->cache->set('admpagesUrlLayout' . Yii::$app->language, $layouts, 86400, $dependency);
            }
            $module::$layoutAliases = $layouts;
        }
        if (isset($module::$layoutAliases[$layout])) {
            $url[$options['key']] = $module::$layoutAliases[$layout];
            $url = ArrayHelper::merge($url, $options['params']);

        } else {
            $url = null;
        }
        return $url;
    }

    /**
     * @param $layout
     * @param bool $scheme
     * @param array $options
     * @return string
     */
    public static function urlToLayout($layout, $scheme = false, $options = [])
    {
        $options  = ArrayHelper::merge([
            'defaultUrl' => '/',
        ], $options);
        $url = static::urlLayout($layout, $options);
        if ($url === null) {
            $url = $options['defaultUrl'];
        }
        return \yii\helpers\Url::to($url, $scheme);
    }

    /**
     * @param $layout
     * @param array $params
     * @param bool $scheme
     * @param array $options
     * @return string
     */
    public static function urlToLayoutParams($layout, $params = [], $scheme = false, $options = [])
    {
        $options  = ArrayHelper::merge([
            'defaultUrl' => '/',
            'params' => $params,
        ], $options);
        $url = static::urlLayout($layout, $options);
        if ($url === null) {
            $url = $options['defaultUrl'];
        }
        return \yii\helpers\Url::to($url, $scheme);
    }

    /**
     * @param $id
     * @param array $options
     * @return array|null
     */
    public static function urlId($id, $options = [])
    {
        $module = Module::getInst();

        $options  = ArrayHelper::merge([
            'url' => true,
            'key' => 'alias',
        ], $options);

        $url = ['/admpages/default/index'];
        if ($options['url'] !== true) {
            $url = $options['url'];
        }

        if ($module::$idAliases === null) {
            $aliases = Yii::$app->cache->get('admpagesUrlId' . Yii::$app->language);
            if ($aliases === false) {
                $aliases =  static::find()->from(['p' => static::tableName()])->select(['l.alias', 'p.id'])
                    ->innerJoin(['l'=> PageLang::tableName()],'l.page_id=p.id AND l.language_id=:language_id',[':language_id' => Yii::$app->getI18n()->getId()])
                    ->where(['p.active' => 1])->all();
                $aliases = ArrayHelper::map($aliases, 'id', 'alias');
                $dependency = new \yii\caching\DbDependency([
                    'sql' => 'SELECT MAX(updated_at) FROM ' . static::tableName(),
                ]);
                Yii::$app->cache->set('admpagesUrlId' . Yii::$app->language, $aliases, 86400, $dependency);
            }
            $module::$idAliases = $aliases;
        }

        if (isset($module::$idAliases[$id])) {
            $url[$options['key']] = $module::$idAliases[$id];
        } else {
            $url = null;
        }
        return $url;
    }

    /**
     * @param $id
     * @param bool $scheme
     * @param array $options
     * @return string
     */
    public static function urlToId($id, $scheme = false, $options = [])
    {
        $options  = ArrayHelper::merge([
            'defaultUrl' => '/',
        ], $options);
        $url = static::urlId($id, $options);
        if ($url === null) {
            $url = $options['defaultUrl'];
        }
        return \yii\helpers\Url::to($url, $scheme);
    }

    /**
     * @param $breadcrumbs
     * @param null $id_parent
     * @param array $options
     */
    public static function breadcrumbsTree(&$breadcrumbs, $id_parent = null, $options = [])
    {
        if (!$id_parent) {
            return;
        }
        $options = ArrayHelper::merge([
            'level' => ArrayHelper::remove($options, 'level', 0),
            'lastLink' => false,
            'url' => ArrayHelper::remove($options, 'url', ['index']),
        ], $options);
        $options['level']++;

        $model = static::find()->where(['id' => $id_parent])->one();
        if($model !== null){
            $id_parent = $model->id;
            if($id_parent == null){
                $id_parent = 0;
            }

            $url = ArrayHelper::merge($options['url'], ['id_parent' => $id_parent]);
            if ($options['level'] == 1 && !$options['lastLink']) {
                $item = $model->name;
            } else {
                $item = ['label' => $model->name, 'url' => $url];
            }
            if($breadcrumbs){
                array_unshift($breadcrumbs, $item);
            } else {
                $breadcrumbs = [$item];
            }
            static::breadcrumbsTree($breadcrumbs, $model->id_parent, $options);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslation()
    {
        $module = Module::getInst();
        return $this->hasOne($module->manager->pageLangClass, ['page_id' => 'id'])->andWhere(['language_id' => Yii::$app->i18n->getId()]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        $module = Module::getInst();
        return $this->hasMany($module->manager->pageLangClass, ['page_id' => 'id'])->indexBy('language_id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(static::className(), ['id' => 'id_parent']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds()
    {
        return $this->hasMany(static::className(), ['id_parent' => 'id']);
    }
}
