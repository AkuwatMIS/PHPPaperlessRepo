<?php

namespace backend\controllers;

use common\components\Helpers\StructureHelper;
use Yii;
use common\models\Cities;
use common\models\search\CitiesSearch;
use yii\caching\FileCache;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * CitiesController implements the CRUD actions for Cities model.
 */
class CacheController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        return Yii::$app->response->redirect(['site/main']);
                    }
                },
                'only' => ['index', 'view', 'create', 'update', '_form','clear-db'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', '_form','clear-db'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Cities models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * Lists all Cities models.
     * @return mixed
     */
    public function actionClearAll()
    {
        $cache = new FileCache();
        $dirs = array_filter(glob($cache->cachePath.'/*'), 'is_dir');

        echo '<pre>';
        print_r( $dirs);
        chmod($cache->cachePath, 0777);
        unlink($dirs[0]);
        die();
        // clean directories of old asset files.
        //FileSystemHelper::clearCache();

        // remove the cache, can be redis for example
        Yii::$app->cache->flush();

        // rebuild the tables schema cache
        //Yii::$app->db->schema->get;
        Yii::$app->db->schema->refresh();
    }

    /**
     * Lists all Cities models.
     * @return mixed
     */
    public function actionClearDb()
    {
        // rebuild the tables schema cache
        Yii::$app->db->schema->getTableNames();
        Yii::$app->db->schema->refresh();
        return $this->redirect(Yii::$app->request->referrer);
    }
}
