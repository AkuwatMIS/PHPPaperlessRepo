<?php

namespace frontend\controllers;

use common\components\Helpers\BlacklistHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use common\models\Cities;
use common\models\Images;
use common\models\Loans;
use common\models\MemberInfo;
use common\models\MembersAccount;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\NadraVerisys;
use common\models\search\MembersSearch;
use Yii;
use common\models\Members;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use common\models\Model;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;

/**
 * CnicExpiryController implements the CRUD actions for Expired CNIC model.
 */
class CnicExpiryController extends Controller
{
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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
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

    /**
     * Lists all Members models.
     * @return mixed
     */
    public function actionIndex()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        if (isset($_GET['export']) && $_GET['export'] == 'export') {
            $this->layout = 'csv';
            $headers = array_keys($_GET['MembersSearch']);
            $groups = array();
            $searchModel = new MembersSearch();
            // $query = $searchModel->searchExpireCNIC(Yii::$app->request->queryParams);
            // Yii::$app->Permission->getSearchFilter($query, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);

            $dataProvider = $searchModel->searchExpireCNIC(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
            
            $i = 0;
            foreach ($data as $g) {
                $groups[$i]['full_name'] = isset($g['full_name']) ? $g['full_name'] : '';
                $groups[$i]['parentage'] = isset($g['parentage']) ? $g['parentage'] : '';
                $groups[$i]['cnic'] = isset($g['cnic']) ? $g['cnic'] : '';
                $groups[$i]['cnic_issue_date'] = date('Y-m-d', isset($g['info']['cnic_issue_date']) ? strtotime($g['info']['cnic_issue_date']) : 0);
                $groups[$i]['cnic_expiry_date'] = date('Y-m-d', isset($g['info']['cnic_expiry_date']) ? strtotime($g['info']['cnic_expiry_date']) : 0);
                $groups[$i]['dob'] = date('Y-m-d', isset($g['dob']) ? $g['dob'] : 0);
                $groups[$i]['education'] = isset($g['education']) ? $g['education'] : '';
                $groups[$i]['marital_status'] = isset($g['marital_status']) ? $g['marital_status'] : '';
                $groups[$i]['status'] = isset($g['status']) ? $g['status'] : '';
                $groups[$i]['region_id'] = isset($g->region->name) ? $g->region->name : '';
                $groups[$i]['area_id'] = isset($g->area->name) ? $g->area->name : '';
                $groups[$i]['branch_id'] = isset($g->branch->name) ? $g->branch->name : '';
                $groups[$i]['team_id'] = isset($g->team->name) ? $g->team->name : '';
                $groups[$i]['field_id'] = isset($g->field->name) ? $g->field->name : '';
                $i++;
            }
            ExportHelper::ExportCSV('members.csv', $headers, $groups);
            die();
        }
        $regions = Yii::$app->Permission->getRegionList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);
        $searchModel = new MembersSearch();
        /*$key = Yii::$app->controller->id.'_'.Yii::$app->controller->action->id;
        $dataProvider = CacheHelper::getData($key);
        if ($dataProvider === false) {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            CacheHelper::setData($dataProvider,$key);
        }*/
        // if (empty(Yii::$app->request->queryParams)) {
        //     $dataProvider = [];
        // } else {
            $dataProvider = $searchModel->searchExpireCNIC(Yii::$app->request->queryParams);
            Yii::$app->Permission->getSearchFilter($dataProvider, Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type);



        //}
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'regions' => $regions,
        ]);
    }

    /**
     * Updates an existing RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post())){
                $model->file = 'abc';
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update RecoveryFiles #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }




    public function actionExports($id){
        $data = RecoveryErrors::find()->select(['bank_branch_name','bank_branch_code','source','sanction_no','cnic','from_unixtime(recv_date, \'%Y-%m-%d\') as recv_date','credit','receipt_no','balance','error_description','comments','status'])->where(['recovery_files_id' => $id])->asArray()->all();
        //$heading = ['bank_branch_name','bank_branch_code','source','sanction_no','recv_date','credit','receipt_no','balance','error_description','comments','status'];

        $heading = array_keys($data[0]);
        $file_model = $this->findModel($id);
        $array = explode('.csv',$file_model->file_name);
        $filename = $array[0]. '_error';
        //$filename = $file_model->source .'_'. date('Ymd',strtotime($file_model->file_date)). '_error';
        \common\components\Helpers\ExportHelper::ExportCSV($filename,$heading,$data);
    }

    /**
     * Delete an existing RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

    }

     /**
     * Delete multiple existing RecoveryFiles model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
    {        

       
    }

    /**
     * Finds the RecoveryFiles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RecoveryFiles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RecoveryFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
