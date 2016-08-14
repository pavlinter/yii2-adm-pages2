<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.1.0
 */

namespace pavlinter\admpages2\controllers;

use pavlinter\admpages2\Module;
use Yii;
use yii\base\InvalidRouteException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @param $alias
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($alias)
    {

        $module = Module::getInst();
        $module->layout = $module->pageLayout;
        /* @var $model \pavlinter\admpages2\models\Page */
        if ($alias === '') {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $model = $module->manager->createPageQuery('getPage', null, [
            'where' => ['alias' => $alias],
            'url' => function ($model, $id_language, $language) {
                if ($model->hasTranslation($id_language)) {
                    $url = $model->url(true, $id_language);
                } else {
                    $url = [''];
                }
                if (is_array($url)) {
                    $url['lang'] = $language[Yii::$app->getI18n()->langColCode];
                }
                return \yii\helpers\Url::to($url);
            },
        ]);
        $module::$modelPage = $model;
        if (isset($module->pageRedirect[$model->layout])) {

            list(,$params) = Yii::$app->request->resolve();
            $newParams = $module->pageRedirect[$model->layout];

            if ($newParams instanceof \Closure) {
                $newParams = call_user_func($newParams, $model, $module);
            }
            $params = ArrayHelper::merge($params, $newParams);
            $route  = ArrayHelper::remove($params, 0);

            $parts = Yii::$app->createController($route);
            if (is_array($parts)) {
                /* @var $controller Controller */
                list($controller, $actionID) = $parts;
                $result = $controller->runAction($actionID, $params);
            } else {
                $id = Yii::$app->getUniqueId();
                throw new InvalidRouteException('Unable to resolve the request "' . ($id === '' ? $route : $id . '/' . $route) . '".');
            }

            if ($result instanceof \yii\web\Response) {
                return $result;
            } else {
                $response = Yii::$app->getResponse();
                if ($result !== null) {
                    $response->data = $result;
                }
                return $response;
            }
        }

        return $this->render('@vendor/pavlinter/yii2-adm-pages2/admpages2/views/default/' . $model->layout, [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMain()
    {
        /* @var $module \pavlinter\admpages2\Module */
        $module = Module::getInst();
        $module->layout = $module->pageLayout;
        /* @var $model \pavlinter\admpages2\models\Page */
        $model = $module->manager->createPageQuery('getPage', null, [
            'where' => ['type' => 'main'],
            'orderBy' => ['weight' => SORT_ASC],
        ]);
        $module::$modelPage = $model;
        if (isset($module->pageRedirect[$model->layout])) {
            $params = $module->pageRedirect[$model->layout];
            if ($params instanceof \Closure) {
                $params = call_user_func($params, $model, $module);
            }
            $route  = ArrayHelper::remove($params, 0);

            $parts = Yii::$app->createController($route);
            if (is_array($parts)) {
                /* @var $controller Controller */
                list($controller, $actionID) = $parts;
                $result = $controller->runAction($actionID, $params);
            } else {
                $id = Yii::$app->getUniqueId();
                throw new InvalidRouteException('Unable to resolve the request "' . ($id === '' ? $route : $id . '/' . $route) . '".');
            }

            if ($result instanceof \yii\web\Response) {
                return $result;
            } else {
                $response = Yii::$app->getResponse();
                if ($result !== null) {
                    $response->data = $result;
                }
                return $response;
            }
        }

        return $this->render('@vendor/pavlinter/yii2-adm-pages2/admpages2/views/default/' . $model->layout, [
            'model' => $model,
        ]);
    }

}
