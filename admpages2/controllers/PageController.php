<?php

/**
 * @package yii2-adm-pages2
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 0.0.1
 */

namespace pavlinter\admpages2\controllers;

use pavlinter\adm\Adm;
use pavlinter\adm\filters\AccessControl;
use pavlinter\admpages2\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{
    /**
    * @inheritdoc
    */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['Adm-Pages'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionFiles($id, $id_parent = null)
    {
        $model = $this->findModel($id);

        $files = Module::getInst()->files;
        $startPath = '';
        if (!isset($files[$model->type])) {
            throw new InvalidConfigException('The "files" property for type(' . $model->type . ') must be set.');
        }
        if (isset($files[$model->type]['startPath'])) {
            $startPath = strtr($files[$model->type]['startPath'], [
                '{id}' => $model->id,
            ]);
        }
        foreach ($files[$model->type]['dirs'] as $path) {
            $dir = Yii::getAlias(strtr($path, [
                '{id}' => $model->id,
            ]));
            \yii\helpers\FileHelper::createDirectory($dir);
        }


        if (!$id_parent) {
            $id_parent = 0;
        }

        $elfinderData = []; //for https://github.com/pavlinter/yii2-adm-app
        $elfinderData['w'] = isset($files[$model->type]['maxWidth']) ? $files[$model->type]['maxWidth'] : 0;
        $elfinderData['h'] = isset($files[$model->type]['maxHeight']) ? $files[$model->type]['maxWidth'] : 0;
        $elfinderData['watermark'] = isset($files[$model->type]['watermark']) && $files[$model->type]['watermark']? 1 : 0;


        return $this->render('files', [
            'model' => $model,
            'startPath' => $startPath,
            'id_parent' => $id_parent,
            'elfinderData' => $elfinderData,
        ]);
    }


    /**
     * Lists all Page models.
     * @param integer|bool $id_parent
     * @return mixed
     */
    public function actionIndex($id_parent = false)
    {
        $searchModel  = Module::getInst()->manager->createPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id_parent);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id_parent' => $id_parent,
        ]);
    }


    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null|integer $id
     * @param null|integer $id_parent
     * @return mixed
     */
    public function actionCreate($id = null, $id_parent = null)
    {
        $model = Module::getInst()->manager->createPage();
        $model->loadDefaultValues();
        $model->setLangScenario('create-page-lang');

        $data = Yii::$app->request->post();
        if ($model->loadAll($data)) {
            if ($model->validateAll()) {

                if ($model->saveAll(false)) {
                    Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully inserted!'));
                    if (!$id_parent) {
                        $id_parent = 0;
                    }
                    return Adm::redirect(['update', 'id' => $model->id, 'id_parent' => $id_parent]);
                }
            }
        } else {
            if($id){
                $model = $this->findModel($id);
                $model->setIsNewRecord(true);
                $model->weight = null;
            } else if($id_parent){
                $model->id_parent = $id_parent;
            }
        }

        return $this->render('create', [
            'model' => $model,
            'id_parent' => $id_parent,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @param null|integer $id_parent
     * @return mixed
     */
    public function actionUpdate($id, $id_parent = null)
    {
        $model = $this->findModel($id);
        $model->setLangScenario('update-page-lang');
        if ($model->loadAll(Yii::$app->request->post()) && $model->validateAll()) {
            if ($model->saveAll(false)) {
                Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully changed!'));
                if (!$id_parent) {
                    $id_parent = 0;
                }
                return Adm::redirect(['update', 'id' => $model->id, 'id_parent' => $id_parent]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'id_parent' => $id_parent,
        ]);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @param null|integer $id_parent
     * @return mixed
     */
    public function actionDelete($id, $id_parent = null)
    {
        if (!in_array($id, Module::getInst()->closeDeletePage)) {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully removed!'));
        }
        $url = ['index', 'id_parent' => 0];
        if ($id_parent !== null) {
            $url['id_parent'] = $id_parent;
        }
        return $this->redirect($url);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return \pavlinter\admpages2\models\Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        $model = Module::getInst()->manager->createPageQuery('find')->with(['translations'])->where(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
