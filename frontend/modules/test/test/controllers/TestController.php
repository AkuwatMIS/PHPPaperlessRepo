<?php

namespace frontend\modules\test\test\controllers;

use common\components\DataListHelper;
use common\components\DBHelper;
use common\components\DBSchemaHelper;
use common\components\DataListSchemaHelper;
use common\components\Helpers\ConfigHelper;
use common\components\RbacHelper;
use common\components\SchemaHelper;
use common\components\ViewFormHelper;
use common\components\ViewFormSchemaHelper;
use common\models\Models;
use common\widgets\Logs;
use yii\caching\MemCache;
use yii\db\Schema;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\TableSchema;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\GeneratorHelper;
use yii\widgets\ActiveForm;


/**
 * ActionsController implements the CRUD actions for Actions model.
 */
class TestController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionImage()
    {
        $path =Yii::$app->basePath . '\web\abc.png' ;
        /*print_r($path);
        die();*/
        //$path = 'myfolder/myimage.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base = base64_encode($data);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        print_r($base);
        die();
    }

    public function actionConfig()
    {
        $config = ConfigHelper::globalConfigs('branch',2);
        print_r($config);
        die();
    }

    public function actionRbac()
    {
       RbacHelper::check();
    }

    public function actionLogs()
    {
        return $this->render('logs');
    }

    public function actionBa()
    {
        DataListHelper::getBADropdowns();
    }

    public function actionDbSchema()
    {
        //DBHelper::uploadFile();
        //DBHelper::getDBTables();
        $db_schema = json_encode(DBSchemaHelper::getDbSchema());
        print_r($db_schema);
        die();
    }

    public function actionTableSchema()
    {
        $table = 'members';
        $table_schema = json_encode(DBSchemaHelper::getTableSchema($table));
        print_r($table_schema);
        die();
    }

    public function actionFormView()
    {
        $section_name = 'members';
        $form_view_schema = json_encode(ViewFormHelper::getSectionsSchema($section_name));
        print_r($form_view_schema);
        die();
    }

    public function actionDatalist()
    {
        $section_name = 'members';
        $table_schema = json_encode(DataListHelper::getDataList($section_name));
        print_r($table_schema);
        die();
    }

    public function actionList($table)
    {
        $data_list = json_encode(DataListHelper::getTableDataList($table));
        print_r($data_list);
        die();
    }


    /**
     * Lists all Actions models.
     * @return mixed
     */
    public function actionModel($table_name)
    {
        $class_name = '';
        $class_array = explode('_', $table_name);
        foreach ($class_array as $c) {
            $class_name .= ucfirst($c);
        }
        $ns = 'common\models';
        $file_path = Yii::getAlias('@common').'\\'.'models\\'.$class_name.'.php';
        if(file_exists($file_path)) {
            print_r('model class already exist');
            die();
        } else {
            $generator = new GeneratorHelper('model');
            $generator->params = ['Generator' => ['tableName' => $table_name, 'modelClass' => $class_name, 'ns' => $ns]];
            $generator->load_params();
            $generator->generate_model();
        }
    }

    public function generateModel($table_name)
    {
        $class_name = '';
        $class_array = explode('_', $table_name);
        foreach ($class_array as $c) {
            $class_name .= ucfirst($c);
        }
        $ns = 'common\models';
        $file_path = Yii::getAlias('@common').'\\'.'models\\'.$class_name.'.php';
        /*if(file_exists($file_path)) {
            print_r('model class already exist');
            die();
        } else {*/
        $generator = new GeneratorHelper('model');
        $generator->params = ['Generator' => ['tableName' => $table_name, 'modelClass' => $class_name, 'ns' => $ns]];
        $generator->load_params();
        $generator->generate_model();
        //}
    }

    public function generateModule()
    {
        $module_class = 'backend\modules\api\Users';
        $module_id = 'api';
        $generator = new GeneratorHelper('module');
        $generator->params = ['Generator' => ['moduleClass' => $module_class, 'moduleID' => $module_id]];
        $generator->load_params();
        $generator->generate_model();
    }

    public function actionCrud($table_name)
    {
        $class_name = '';
        $class_array = explode('_', $table_name);
        foreach ($class_array as $c) {
            $class_name .= ucfirst($c);
        }
        $model_class = 'common\models\\' . $class_name;
        $search_class = 'common\models\search\\' . $class_name . 'Search';
        $controller_class = 'backend\controllers\\' . $class_name . 'Controller';

        /*$model_class = 'common\models\Bank';
        $controller_class = 'backend\controllers\BankController';
        $search_class = 'common\models\search\BankSearch';*/
        $generator = new GeneratorHelper('crud');
        $generator->params = ['Generator' => ['modelClass' => $model_class, 'controllerClass' => $controller_class, 'searchModelClass' => $search_class]];
        $generator->load_params();
        $generator->generate_model();
    }

    public function generateCrud($table_name)
    {
        $class_name = '';
        $class_array = explode('_', $table_name);
        foreach ($class_array as $c) {
            $class_name .= ucfirst($c);
        }
        $model_class = 'common\models\\' . $class_name;
        $search_class = 'common\models\search\\' . $class_name . 'Search';
        $controller_class = 'backend\controllers\\' . $class_name . 'Controller';

        /*$model_class = 'common\models\Bank';
        $controller_class = 'backend\controllers\BankController';
        $search_class = 'common\models\search\BankSearch';*/
        $generator = new GeneratorHelper('crud');
        $generator->params = ['Generator' => ['modelClass' => $model_class, 'controllerClass' => $controller_class, 'searchModelClass' => $search_class]];
        $generator->load_params();
        $generator->generate_model();
    }

    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public function actionTablesList()
    {
        $tables_list = DBHelper::getDBTables();
        return $this->render('tables_list',[
            'tables_list' => $tables_list,
        ]);
    }

    public function actionCreate()
    {
        $db = Yii::$app->getDb();
        $dbName = $this->getDsnAttribute('dbname', $db->dsn);
        $table_type = ['project_details'=>'project_details','business_appraisal'=>'business_appraisal','borrower_custom' => 'borrower_custom'];
        $default = ['None' => 'None', 'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP', 'Null' => 'Null'];
        $form_data = Yii::$app->request->post();
        if ($form_data) {
            $arr = [];
            $attributes = $form_data['att'];
            $table_name = $form_data['table_type'].'_'.$form_data['table_name'];
            foreach ($attributes as $attribute)
            {
                $str = '';
                if(isset($attribute['data_type'])) {
                    $str .= $attribute['data_type']. '';
                }

                if(isset($attribute['length'])) {
                    if($attribute['length'] != null) {
                        $str .= '(' . $attribute['length'] . ') ';
                    }
                }

                if(!isset($attribute['is_null'])) {
                    $str .= ' NOT NULL ';
                }

                if(isset($attribute['default_value'])) {
                    if($attribute['default_value'] != 'None') {
                        $str .= ' DEFAULT ' . $attribute['default_value'];
                    }
                }
                $arr['id'] = 'pk';
                if(isset($attribute['col_name'])) {
                    $arr[$attribute['col_name']]= $str;
                }
                $arr['assigned_to'] = 'INT NOT NULL';
                $arr['created_by'] = 'INT NOT NULL';
                $arr['created_at'] = ' DATETIME NOT NULL';
                $arr['updated_at'] = 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';
            }
            $this->generateTable($table_name, $arr);
            if(isset($form_data['gen_model']))
            {
                $this->generateModel($table_name);
            }
            if(isset($form_data['gen_crud']))
            {
                $this->generateCrud($table_name);
            }
            /*$table_name = $form_data['table_name'];
            $class_name = '';
            $class_array = explode('_', $table_name);
            foreach ($class_array as $c) {
                $class_name .= ucfirst($c);
            }
            $model_class = 'common\models\\' . $class_name;
            $ns = 'common\models';
            $search_class = 'common\models\search\\' . $class_name . 'Search';
            $controller_class = 'backend\controllers\\' . $class_name . 'Controller';
            $model->table_name = $table_name;
            $model->class_name = $class_name;
            $model->ns = $ns;
            $model->model_class = $model_class;
            $model->controller_class = $controller_class;
            $model->search_class = $search_class;
            $model->set_values();
            //$model->save();
            echo '<pre>';
            print_r($model);
            die();*/
            return $this->redirect('tables-list');
        }
        else {
            return $this->render('create', [
                'types' => $this->getTypes(),
                'default' => $default,
                'table_type' => $table_type,

            ]);
        }
    }

    public function actionUpdate($table_name)
    {
        $arr = ['id','assigned_to','created_by', 'created_at', 'updated_at'];
        $table_attr = [];
        $table_schema = Yii::$app->db->getTableSchema($table_name);
        $column_data = $table_schema->columns;
        foreach($column_data as $k=>$data) {
            if(!in_array($data->name,$arr)) {
                //print_r($data->name . ' ' . $data->allowNull . ' ' . $data->type);
                $table_attr[$data->name] = $data;
            }
        }

        $attr_list = [] ;

        $table_type = ['project_details'=>'project_details','business_appraisal'=>'business_appraisal','borrower_custom' => 'borrower_custom'];
        $default = ['None' => 'None', 'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP', 'Null' => 'Null'];
        $form_data = Yii::$app->request->post();
        if ($form_data) {
            $attributes = $form_data['att'];
            foreach ($attributes as $attr)
            {
                $attr_list[] = $attr['col_name'];
            }
            $table_attr_names = $table_schema->getColumnNames();
            foreach ($table_attr_names as $table_attr_name) {
                if (!in_array($table_attr_name, $arr) && !in_array($table_attr_name, $attr_list)) {
                    Yii::$app->db->createCommand()->dropColumn($table_name,$table_attr_name)->execute();
                }
            }
            foreach ($attributes as $attribute) {
                $str = '';
                if (isset($attribute['data_type'])) {
                    $str .= $attribute['data_type'] . '';
                }

                if (isset($attribute['length'])) {
                    if ($attribute['length'] != null) {
                        $str .= '(' . $attribute['length'] . ') ';
                    }
                }

                if (!isset($attribute['is_null'])) {
                    $str .= ' NOT NULL ';
                }

                if (isset($attribute['default_value'])) {
                    if ($attribute['default_value'] != 'None') {
                        $str .= ' DEFAULT ' . $attribute['default_value'];
                    }
                }
                if (isset($attribute['col_name'])) {
                    //$arr[$attribute['col_name']]= $str;
                    if (in_array($attribute['col_name'], $table_schema->getColumnNames())) {
                        Yii::$app->db->createCommand()->alterColumn($table_name, $attribute['col_name'], $str)->execute();
                    } else {
                        Yii::$app->db->createCommand()->addColumn($table_name, $attribute['col_name'], $str)->execute();
                    }
                }
            }
            if(isset($form_data['gen_model']))
            {
                $this->generateModel($table_name);
            }
            if(isset($form_data['gen_crud']))
            {
                $this->generateCrud($table_name);
            }
            print_r('update successfully');
            die();
        }
        else {
            return $this->render('update', [
                'types' => $this->getTypes(),
                'default' => $default,
                'table_name' => $table_name,
                'table_attr' => $table_attr,

            ]);
        }
    }

    public function generateTable($table_name,$attributes)
    {
        Yii::$app->db->createCommand()->createTable($table_name, $attributes)->execute();
    }

    public function getTypes_()
    {
        return [
            'bigint' => 'bigint',
            'binary' => 'binary',
            'bit' => 'bit',
            'char' => 'char',
            'date' => 'date',
            'datetime' => 'datetime',
            'decimal' => 'decimal',
            'float' => 'float',
            'geography' => 'geography',
            'geometry' => 'geometry',
            'hierarchyid' => 'hierarchyid',
            'image' => 'image',
            'int' => 'int',
            'money' => 'money',
            'nchar' => 'nchar',
            'ntext' => 'ntext',
            'numeric' => 'numeric',
            'nvarchar' => 'nvarchar',
            'real' => 'real',
            'smalldatetime' => 'smalldatetime',
            'smallint' => 'smallint',
            'smallmoney' => 'smallmoney',
            'sql_variant' => 'sql_variant',
            'text' => 'text',
            'time' => 'time',
            'timestamp' => 'timestamp',
            'tinyint' => 'tinyint',
            'uniqueidentifier' => 'uniqueidentifier',
            'varbinary' => 'varbinary',
            'varchar' => 'varchar',
            'xml' => 'xml',
        ];
    }

    public function getTypes()
    {
        return [
            Schema::TYPE_STRING => 'String',
            Schema::TYPE_TEXT => 'Text',
            Schema::TYPE_INTEGER => 'Integer',
            Schema::TYPE_SMALLINT => 'Small Integer',
            Schema::TYPE_BIGINT => 'Big Integer',
            Schema::TYPE_FLOAT => 'Float',
            //Schema::TYPE_DOUBLE => 'Double',
            Schema::TYPE_DECIMAL => 'Decimal',
            Schema::TYPE_DATETIME => 'DateTime',
            Schema::TYPE_TIMESTAMP => 'Timestamp',
            Schema::TYPE_TIME => 'Time',
            Schema::TYPE_DATE => 'Date',
            Schema::TYPE_BINARY => 'Binary',
            Schema::TYPE_BOOLEAN => 'Boolean',
            Schema::TYPE_CHAR => 'Char',
            //Schema::TYPE_MONEY => 'Money',
        ];
    }

    public function lengthRequired()
    {
        return  in_array(
            $this->type, [
            Schema::TYPE_STRING,
            Schema::TYPE_INTEGER,
            Schema::TYPE_SMALLINT,
            Schema::TYPE_BIGINT,
            Schema::TYPE_BINARY,
            Schema::TYPE_CHAR,
        ]);
    }

}
