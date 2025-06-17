<?php

namespace frontend\modules\test\test\controllers;

use common\models\ViewSectionFields;
use common\components\DBHelper;
use common\models\SectionFieldsConfigs;
use Yii;
use common\models\ViewSections;
use common\models\search\ViewSectionsSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ViewSectionsController implements the CRUD actions for ViewSections model.
 */
class ViewSectionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulkdelete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ViewSections models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ViewSectionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single ViewSections model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $section_fields = $model->viewSectionFields;
        //$fields_configs = $section_fields->sectionFieldsConfigs;
        /*print_r($fields_configs);
        die();*/
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "ViewSections #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $model,
                    'section_fields' => $section_fields,
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
            ];
        } else {
            return $this->render('view', [
                'model' => $model,
                'section_fields' => $section_fields,
            ]);
        }
    }

    /**
     * Creates a new ViewSections model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ViewSections();

        $modelsFields = [new ViewSectionFields];
        //$modelsFields = array_fill(0, 3, $modelsField);

        $modelsFieldsConfigs = [[new SectionFieldsConfigs]];
      // $modelsFieldsConfigs = array_fill(0, 3, $modelsFieldsConfigs);
        $tables_list = DBHelper::getDBTables();

        if ($model->load($request->post())) {
            /*echo '<pre>';
            print_r($model);
            die();*/
            $modelsFields = ViewSections::createMultiple(ViewSectionFields::classname());
            ViewSections::loadMultiple($modelsFields, Yii::$app->request->post());

            $model->set_values();
            $valid = $model->validate();
            $valid = ViewSections::validateMultiple($modelsFields) && $valid;
            if (isset($_POST['SectionFieldsConfigs'][0][0])) {
                foreach ($_POST['SectionFieldsConfigs'] as $indexField => $fieldsConfigs) {
                    foreach ($fieldsConfigs as $indexFieldsConfigs => $fieldsConfig) {
                        $data['SectionFieldsConfigs'] = $fieldsConfig;
                        $modelFieldsConfigs = new SectionFieldsConfigs();
                        $modelFieldsConfigs->load($data);
                        $modelsFieldsConfigs[$indexField][$indexFieldsConfigs] = $modelFieldsConfigs;
                        $valid = $modelFieldsConfigs->validate();
                    }
                }
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save(false)) {
                    foreach ($modelsFields as $indexField => $modelField) {

                        if ($flag === false) {
                            die('model not save');
                            break;
                        }
                        $modelField->section_id = $model->id;
                        $modelField->set_values();
                        if (!($flag = $modelField->save(false))) {
                            break;
                        }

                        if (isset($modelsFieldsConfigs[$indexField]) && is_array($modelsFieldsConfigs[$indexField])) {
                            foreach ($modelsFieldsConfigs[$indexField] as $indexFieldsConfigs => $modelFieldsConfig) {
                                $modelFieldsConfig->field_id = $modelField->id;
                                $modelFieldsConfig->set_values();

                                if (!($flag = $modelFieldsConfig->save(false))) {
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }

        } else {
            return $this->render('create', [
                'model' => $model,
                'modelsFields' => (empty($modelsFields)) ? [new ViewSectionFields] : $modelsFields,
                'modelsFieldsConfigs' => (empty($modelsFieldsConfigs)) ? [[new SectionFieldsConfigs]] : $modelsFieldsConfigs,
                'tables_list' => $tables_list,
            ]);

        }

    }

    /**
     * Updates an existing ViewSections model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tables_list = DBHelper::getDBTables();

        $modelsFields = $model->viewSectionFields;
        $modelsFieldsConfigs = [];
        $OldFieldsConfigs = [];

        if (!empty($modelsFields)) {
            foreach ($modelsFields as $indexField => $modelFields) {
                $fieldsConfigs = $modelFields->sectionFieldsConfigs;
                $modelsFieldsConfigs[$indexField] = $fieldsConfigs;
                $OldFieldsConfigs = ArrayHelper::merge(ArrayHelper::index($fieldsConfigs, 'id'), $OldFieldsConfigs);
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            // reset
            $modelsFieldsConfigs = [];

            $oldSectionFieldsIDs = ArrayHelper::map($modelsFields, 'id', 'id');
            $modelsFields = ViewSections::createMultiple(ViewSectionFields::classname(), $modelsFields);
            ViewSections::loadMultiple($modelsFields, Yii::$app->request->post());
            $deletedSectionFieldsIDs = array_diff($oldSectionFieldsIDs, array_filter(ArrayHelper::map($modelsFields, 'id', 'id')));

            $valid = $model->validate();
            $valid = ViewSections::validateMultiple($modelsFields) && $valid;

            $FieldsConfigsIDs = [];
            if (isset($_POST['SectionFieldsConfigs'][0][0])) {
                foreach ($_POST['SectionFieldsConfigs'] as $indexField => $fieldsConfigs) {

                    $FieldsConfigsIDs = ArrayHelper::merge($FieldsConfigsIDs, array_filter(ArrayHelper::getColumn($fieldsConfigs, 'id')));
                    foreach ($fieldsConfigs as $indexFieldsConfig => $fieldsConfig) {
                        $data['SectionFieldsConfigs'] = $fieldsConfig;
                        $modelFieldsConfigs = (isset($fieldsConfig['id']) && isset($OldFieldsConfigs[$fieldsConfig['id']])) ? $OldFieldsConfigs[$fieldsConfig['id']] : new SectionFieldsConfigs;
                        $modelFieldsConfigs->load($data);
                        $modelsFieldsConfigs[$indexField][$indexFieldsConfig] = $modelFieldsConfigs;
                        $valid = $modelFieldsConfigs->validate();
                    }
                }
            }

            $oldFieldConfigsIDs = ArrayHelper::getColumn($OldFieldsConfigs, 'id');
            $deletedFieldsConfigsIDs = array_diff($oldFieldConfigsIDs, $FieldsConfigsIDs);

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                        if (!empty($deletedFieldsConfigsIDs)) {
                            SectionFieldsConfigs::deleteAll(['id' => $deletedFieldsConfigsIDs]);
                        }

                        if (!empty($deletedSectionFieldsIDs)) {
                            ViewSectionFields::deleteAll(['id' => $deletedSectionFieldsIDs]);
                        }

                        foreach ($modelsFields as $indexField => $modelsField) {

                            if ($flag === false) {
                                break;
                            }

                            $modelsField->section_id = $model->id;
                            $modelsField->set_values();
                            if (!($flag = $modelsField->save(false))) {

                                break;
                            }

                            if (isset($modelsFieldsConfigs[$indexField]) && is_array($modelsFieldsConfigs[$indexField])) {
                                foreach ($modelsFieldsConfigs[$indexField]  as $indexFieldsConfigs => $modelFieldsConfig) {
                                    $modelFieldsConfig->field_id = $modelsField->id;
                                    $modelFieldsConfig->set_values();
                                    //echo '<pre>';
                                    //print_r($modelFieldsConfig);
                                    //die();
                                    if (!($flag = $modelFieldsConfig->save(false))) {

                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        //die();
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }

        }

        return $this->render('update', [
            'model' => $model,
            'modelsFields' => (empty($modelsFields)) ? [new ViewSectionFields] : $modelsFields,
            'modelsFieldsConfigs' => (empty($modelsFieldsConfigs)) ? [[new SectionFieldsConfigs]] : $modelsFieldsConfigs,
            'tables_list' => $tables_list,
        ]);
    }

    /**
     * Delete an existing ViewSections model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $fields = ViewSectionFields::find()->select(['id'])->where(['section_id' => $model->id])->asArray()->all();
        foreach ($fields as $field)
        {
            SectionFieldsConfigs::deleteAll(['field_id' => $field['id']]);
        }
        ViewSectionFields::deleteAll(['section_id' => $model->id]);
        $model->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

     /**
     * Delete multiple existing ViewSections model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkdelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }

    public function actionFetchColumnsByTable()
    {
        $out = [];
        $parents = Yii::$app->request->post('depdrop_parents');

        if ($parents && $parents[0]) {
            /*print_r($parents);
            die();*/
            $rgId = $parents[0];
            $out = DBHelper::getTableColumns($rgId);
            echo Json::encode(['output'=>$out, 'selected'=>'']);
            return;
        }

        echo Json::encode(['output'=>'', 'selected'=>'']);
        return;
    }

    public function actionFetchKeysByColumn1()
    {

        if (isset($_POST['keylist'])) {
           // echo '<pre>';
            //print_r($_POST);
            //die();
            $out = DBHelper::getParentId($_POST['keylist'][0], $_POST['keylist'][1], $_POST['keylist'][2]);
            $selected = DBHelper::getParent($_POST['keylist'][0], $_POST['keylist'][1], $_POST['keylist'][2], $_POST['keylist'][3]);
            echo Json::encode(['output'=>$out, 'selected'=>$selected]);
            return;
        }
        /*$out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        /*$out = DBHelper::getParentId($table, $field);
        echo Json::encode(['output'=>$out, 'selected'=>'']);
        return;
        if ($parents && !empty($parents[0]) && !empty($parents[1])) {
            $rgId = $parents[0];
            $rgId1 = $parents[1];
            $out = DBHelper::getParentId($rgId, $rgId1);
            echo Json::encode(['output'=>$out, 'selected'=>'']);
            return;
        }
        echo Json::encode(['output'=>$out, 'selected'=>'']);
        return;*/
    }

    public function actionFetchKeysByColumn()
    {
        $s =['id' => "3", 'name' => 'format'];
        if (isset($_POST['keylist'])) {
            $out = DBHelper::getParentId($_POST['keylist'][0], $_POST['keylist'][1], $_POST['keylist'][2]);
            echo Json::encode(['output'=>$out]);
            return;
        }
        /*$out = [];
        $parents = Yii::$app->request->post('depdrop_parents');
        /*$out = DBHelper::getParentId($table, $field);
        echo Json::encode(['output'=>$out, 'selected'=>'']);
        return;
        if ($parents && !empty($parents[0]) && !empty($parents[1])) {
            $rgId = $parents[0];
            $rgId1 = $parents[1];
            $out = DBHelper::getParentId($rgId, $rgId1);
            echo Json::encode(['output'=>$out, 'selected'=>'']);
            return;
        }
        echo Json::encode(['output'=>$out, 'selected'=>'']);
        return;*/
    }
    /**
     * Finds the ViewSections model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ViewSections the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ViewSections::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
