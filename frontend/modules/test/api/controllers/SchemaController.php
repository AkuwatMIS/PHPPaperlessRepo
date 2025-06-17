<?php

namespace frontend\modules\test\api\controllers;


use common\components\DataListHelper;
use common\components\DBSchemaHelper;
use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\UsersHelper;
use common\components\ViewFormHelper;
use common\models\Appraisals;
use common\models\Users;
use frontend\modules\test\api\models\Member;
use yii\filters\AccessControl;
use frontend\modules\test\api\models\Employee;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;
use Yii;
use yii\web\Response;

/**
 * Default controller for the `test` module
 */
class SchemaController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                /*'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],*/
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'database' => ['GET'],
                        'badropdowns' => ['GET'],
                        'form' => ['GET'],
                        'lists' => ['GET'],
                        'list' => ['GET'],
                        'documents' => ['GET'],
                        'appraisal' => ['GET']

                    ],
                ],

            ];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionDatabase()
    {
        //$response = ['asd' => '1','dsas' => 'daas'];
        $d["1"] = "CREATE TABLE members(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,full_name TEXT NOT NULL,parentage TEXT,parentage_type TEXT NOT NULL,cnic TEXT NOT NULL UNIQUE,gender TEXT NOT NULL,dob  NOT NULL Default 0,education TEXT NOT NULL,marital_status TEXT,family_no TEXT,family_member_name TEXT,family_member_cnic TEXT,family_member_left_thumb TEXT,family_member_right_thumb TEXT,referral_id INTEGER,religion TEXT NOT NULL,is_disable INTEGER NOT NULL,disability_type TEXT,profile_pic TEXT,status TEXT NOT NULL);CREATE TABLE applications(server_id INTEGER,sync_status INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,fee INTEGER Default 0,application_no TEXT,project_id INTEGER NOT NULL,project_table TEXT,activity_id INTEGER NOT NULL Default 0,product_id INTEGER NOT NULL,bzns_cond TEXT,who_will_work TEXT,name_of_other TEXT,other_cnic TEXT,req_amount INTEGER NOT NULL,status TEXT NOT NULL,is_urban INTEGER NOT NULL,reject_type TEXT,reject_reason TEXT,comments TEXT, FOREIGN KEY (member_id ) REFERENCES members (id)); CREATE TABLE biometric(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER); CREATE TABLE documents(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER); CREATE TABLE appraisals_social(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,house_ownership TEXT NOT NULL,house_rent_amount INTEGER,land_size INTEGER NOT NULL,total_family_members INTEGER NOT NULL,no_of_earning_hands INTEGER,ladies INTEGER NOT NULL,gents INTEGER NOT NULL,source_of_income TEXT NOT NULL,total_household_income INTEGER NOT NULL,utility_bills INTEGER NOT NULL,educational_expenses INTEGER NOT NULL,medical_expenses INTEGER NOT NULL,kitchen_expenses INTEGER NOT NULL,monthly_savings TEXT NOT NULL,amount INTEGER,date_of_maturity INTEGER,other_expenses INTEGER NOT NULL,total_expenses INTEGER,other_loan INTEGER NOT NULL,loan_amount INTEGER,economic_dealings TEXT NOT NULL,social_behaviour TEXT NOT NULL,fatal_disease INTEGER NOT NULL Default 0,business_income INTEGER NOT NULL Default 0,job_income INTEGER NOT NULL Default 0,house_rent_income INTEGER NOT NULL Default 0,other_income INTEGER NOT NULL Default 0,expected_increase_in_income INTEGER,parent TEXT,family_member_info TEXT,earning_hands_data TEXT,social_appraisal_address TEXT,description TEXT,description_image TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id), FOREIGN KEY (application_id ) REFERENCES applications (id)); CREATE TABLE appraisals_business(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,place_of_business TEXT NOT NULL,fixed_business_assets TEXT,fixed_business_assets_amount INTEGER,running_capital TEXT,running_capital_amount INTEGER,business_expenses TEXT,business_expenses_amount INTEGER,new_required_assets TEXT NOT NULL,new_required_assets_amount INTEGER NOT NULL,place_of_buying TEXT,period_of_business INTEGER,who_are_customers TEXT,emp_before_loan INTEGER,emp_after_loan INTEGER,business_appraisal_address TEXT,description TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id), FOREIGN KEY (application_id ) REFERENCES applications (id)); CREATE TABLE members_address(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,home_address TEXT NOT NULL,business_address TEXT); CREATE TABLE members_email(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,email TEXT NOT NULL); CREATE TABLE members_phone(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,mobile TEXT NOT NULL,phone TEXT);CREATE TABLE projects_agriculture(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,owner TEXT,land_area_size INTEGER,land_area_type TEXT,village_name TEXT,uc_number TEXT,uc_name TEXT,crop_type TEXT,crops TEXT); CREATE TABLE projects_disabled(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,is_khidmat_card_holder INTEGER NOT NULL,loan_id INTEGER,disability TEXT,nature TEXT,physical_disability TEXT,visual_disability TEXT,communicative_disability TEXT,disabilities_instruments TEXT); CREATE TABLE projects_tevta(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,institute_name TEXT,type_of_diploma TEXT,duration_of_diploma TEXT,year TEXT,pbte_or_ttb TEXT,registration_no TEXT,roll_no TEXT);";
        $d["2"] = "DROP TABLE documents;";
        $d["3"] = "CREATE TABLE documents(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER);";
        $d["4"] = "DROP TABLE biometric;";
        $d["5"] = "CREATE TABLE biometric(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER);";
        $d["6"] = "CREATE TABLE appraisals_housing(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,property_type TEXT NOT NULL,ownership TEXT NOT NULL,land_area INTEGER NOT NULL,residential_area INTEGER NOT NULL,living_duration INTEGER NOT NULL,duration_type TEXT NOT NULL,no_of_rooms INTEGER NOT NULL,no_of_kitchens INTEGER NOT NULL,no_of_toilets INTEGER NOT NULL,purchase_price INTEGER NOT NULL,current_price INTEGER NOT NULL,address TEXT NOT NULL,estimated_figures TEXT NOT NULL,estimated_start_date INTEGER NOT NULL,estimated_completion_time INTEGER NOT NULL,housing_appraisal_address TEXT,description TEXT,description_image TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id));";
        $response['data'] = $d;
        /*$db_schema = DBSchemaHelper::getDbSchema();
        $response['data'] = $db_schema;
        $response['info'] = [];*/
       // $response[] = 'db_schema/paperless_schema.json';
        return $this->sendSuccessResponse(200, $response['data']);
    }

    public function actionBadropdowns()
    {
        $dropdowns = DataListHelper::getBADropdowns();
        $response['data'] = $dropdowns;
        $response['info'] = [];
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
    }

    public function actionForm()
    {
        $input = $_GET;
        $form_view = ViewFormHelper::getSectionsSchema($input['table']);
        /*if($input['table'] == 'applications')
        {
            Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../../../'));
            $file_name = 'applications_json.txt';
            $file_path = Yii::getAlias('@anyname').'/web'.'/uploads/'.$file_name;
            $form_view =(array) json_decode(file_get_contents($file_path));

        }*/

        /*if($input['table'] == 'applications') {
            $form_view['sections'][0]['questions'][1]['type'] = 'search-input';
        }*/

        $response['data'] =  $form_view;
        $response['info'] = [];
        /*print_r($response);
        die();*/
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
    }

    public function actionDocuments()
    {
        $input = $_GET;
        $documents = ViewFormHelper::getDocuments($input['table']);
        $response['data'] =  $documents;
        $response['info'] = [];
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
    }

    public function actionLists()
    {
        $input = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $data_list = DataListHelper::getDataList($input['table'],$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);
        /*if($input['table'] == 'applications')
        {
            Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../../../'));
            $file_name = 'application_list.txt';
            $file_path = Yii::getAlias('@anyname').'/web'.'/uploads/'.$file_name;
            $data_list =(array) json_decode(file_get_contents($file_path));

        }*/
        $response['data'] = $data_list;
        $response['info'] = [];
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
    }

    public function actionList()
    {
        $input = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $data_list = DataListHelper::getTableDataList($input['table'],$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);
        if($input['table'] == 'appraisals_social' || $input['table'] == 'appraisals_business')
        {
            $response['data'][$input['table']] = $data_list;
        } else {
            $response['data'] = $data_list;
        }
        $response['info'] = [];
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
    }

    public function actionAppraisal()
    {
        $response[] = 'appraisals_json/appraisal_data_test_31.json.gz';
        return $this->sendSuccessResponse(200,$response);
    }
}
