<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.0
 */

namespace pavlinter\admpages2\models;

use pavlinter\admpages2\Module;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%page_lang}}".
 *
 * @property string $id
 * @property string $page_id
 * @property integer $language_id
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $alias
 * @property string $text
 *
 * @property Language $language
 * @property Page $page
 */
class PageLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'description', 'url'], 'filter', 'filter' => function ($value) {
                return Html::encode($value);
            }],
            [['name'], 'required'],
            [['page_id', 'language_id'], 'integer'],
            [['text', 'short_text'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 70],
            [['url'], 'string', 'max' => 2000],
            [['description', 'alias'], 'string', 'max' => 200],
            [['alias'], 'match', 'pattern' => '/^([A-Za-z0-9_-])+$/'],
            [['alias'], 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord || $this->scenario == 'update-page-lang') {
                    $query->andWhere(['=', 'language_id', $this->language_id]);
                    $query->andWhere(['!=', 'page_id', $this->page_id]);
                }
                return $query;
            }],
        ];
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update-page-lang'] = $scenarios['create-page-lang'] = $scenarios[self::SCENARIO_DEFAULT];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modelAdm/admpages', 'ID'),
            'page_id' => Yii::t('modelAdm/admpages', 'Page ID'),
            'language_id' => Yii::t('modelAdm/admpages', 'Language ID'),
            'name' => Yii::t('modelAdm/admpages', 'Name'),
            'title' => Yii::t('modelAdm/admpages', 'Title'),
            'description' => Yii::t('modelAdm/admpages', 'Description'),
            'alias' => Yii::t('modelAdm/admpages', 'Alias'),
            'short_text' => Yii::t('modelAdm/admpages', 'Short Text'),
            'text' => Yii::t('modelAdm/admpages', 'Text'),
        ];
    }

    /**
     * @param $url
     * @param string $key
     * @return mixed
     */
    public function url($url, $key = 'alias')
    {
        if ($this->url) {
            return $this->url;
        }
        if ($key) {
            $url[$key] = $this->alias;
        }
        return $url;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        $module = Module::getInst();
        return $this->hasOne($module->manager->pageClass, ['id' => 'page_id']);
    }
}
