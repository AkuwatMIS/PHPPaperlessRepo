<?php

namespace frontend\controllers;


use common\components\Helpers\StructureHelper;
use common\models\FieldAreas;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

/**
 * ApplicationsController implements the CRUD actions for Applications model.
 */
class FieldAreasController extends Controller
{
    public $enableCsrfValidation = false;
    public $rbac_type = 'frontend';
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
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }


    public function actionCreate()
    {
        $branches = ArrayHelper::map(StructureHelper::getBranches(),'id','name');

        return $this->render('create',['branches' => $branches]);
    }

    public function actionUpload()
    {
        $this->layout = false;
        if (isset($_POST['keylist']) && isset($_POST['team_id'])) {
            $data = json_decode($_POST['keylist'],true);
            foreach ($data as $k => $val)
            {
                $model = new FieldAreas();
                $model->team_id = $_POST['team_id'];
                $model->name = $val['formatted_address'];
                $model->longitude = $val['longitude'];
                $model->latitude = $val['latitude'];
                $model->save();
            }
            $field_areas = FieldAreas::find()->select(['name','longitude','latitude'])->where(['team_id' => $_POST['team_id']])->all();
            return $this->render('list', ['field_areas' => $field_areas, 'team_id' => $_POST['team_id']]);
        }
    }

    public function actionDelete()
    {
        $this->layout = false;
        if(isset($_POST['team_id']) && isset($_POST['field_area']))
        {
            $model = FieldAreas::find()->where(['team_id' => $_POST['team_id'], 'name' => $_POST['field_area']])->one();
            $model->delete();
            $field_areas = FieldAreas::find()->select(['name','longitude','latitude'])->where(['team_id' => $_POST['team_id']])->all();
            return $this->render('list', ['field_areas' => $field_areas, 'team_id' => $_POST['team_id']]);
        }
    }
}
