Yii2: Adm-Pages 2 Модуль для Adm CMS
================

Установка
------------
Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

```
   "pavlinter/yii2-adm-pages2": "*",
```

Настройка
-------------
```php
'modules' => [
    ...
    'adm' => [
        ...
        'modules' => [
            'admpages'
        ],
        ...
    ],
    'admpages' => [
        'class' => 'pavlinter\admpages2\Module',
        'pageLayouts' => [
           'page' => 'Page',
           'page-image' => 'Page + image',
        ],
        'pageRedirect' => [],
        'pageTypes' => [],
        'pageLayout' => '/main',
        'closeDeletePage' => [] //id [2,130]
    ],
    ...
],
'components' => [
    ...
    'urlManager' => [
        ....
        'rules'=>[
            '' => 'admpages/default/main', //OR $config['defaultRoute'] = 'admpages/default/main';
            'page/<alias:([A-Za-z0-9_-])+>' => 'admpages/default/index',
        ],
    ],
    ...
],
```

Запустить миграцию
-------------
```php
yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-pages2/admpages2/migrations
```

Как использовать
-------------
```php
echo Html::a('My-Page',['/admpages/default/index', 'alias' => 'My-Page']);

echo Page::urlToLayout('contact'); //Возращяет ссылку на страницу, где layout contact
```