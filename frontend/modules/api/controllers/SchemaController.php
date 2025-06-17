<?php

namespace frontend\modules\api\controllers;


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

       /* $db_schema = DBSchemaHelper::getDbSchema();
        $response['data'] = $db_schema;*/
        $d["1"] = "CREATE TABLE members(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,full_name TEXT NOT NULL,parentage TEXT,parentage_type TEXT NOT NULL,cnic TEXT NOT NULL UNIQUE,gender TEXT NOT NULL,dob  NOT NULL Default 0,education TEXT NOT NULL,marital_status TEXT,family_no TEXT,family_member_name TEXT,family_member_cnic TEXT,family_member_left_thumb TEXT,family_member_right_thumb TEXT,referral_id INTEGER,religion TEXT NOT NULL,is_disable INTEGER NOT NULL,disability_type TEXT,profile_pic TEXT,status TEXT NOT NULL);CREATE TABLE applications(server_id INTEGER,sync_status INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,fee INTEGER Default 0,application_no TEXT,project_id INTEGER NOT NULL,project_table TEXT,activity_id INTEGER NOT NULL Default 0,product_id INTEGER NOT NULL,bzns_cond TEXT,who_will_work TEXT,name_of_other TEXT,other_cnic TEXT,req_amount INTEGER NOT NULL,status TEXT NOT NULL,is_urban INTEGER NOT NULL,reject_type TEXT,reject_reason TEXT,comments TEXT, FOREIGN KEY (member_id ) REFERENCES members (id)); CREATE TABLE biometric(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER); CREATE TABLE documents(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER); CREATE TABLE appraisals_social(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,house_ownership TEXT NOT NULL,house_rent_amount INTEGER,land_size INTEGER NOT NULL,total_family_members INTEGER NOT NULL,no_of_earning_hands INTEGER,ladies INTEGER NOT NULL,gents INTEGER NOT NULL,source_of_income TEXT NOT NULL,total_household_income INTEGER NOT NULL,utility_bills INTEGER NOT NULL,educational_expenses INTEGER NOT NULL,medical_expenses INTEGER NOT NULL,kitchen_expenses INTEGER NOT NULL,monthly_savings TEXT NOT NULL,amount INTEGER,date_of_maturity INTEGER,other_expenses INTEGER NOT NULL,total_expenses INTEGER,other_loan INTEGER NOT NULL,loan_amount INTEGER,economic_dealings TEXT NOT NULL,social_behaviour TEXT NOT NULL,fatal_disease INTEGER NOT NULL Default 0,business_income INTEGER NOT NULL Default 0,job_income INTEGER NOT NULL Default 0,house_rent_income INTEGER NOT NULL Default 0,other_income INTEGER NOT NULL Default 0,expected_increase_in_income INTEGER,parent TEXT,family_member_info TEXT,earning_hands_data TEXT,social_appraisal_address TEXT,description TEXT,description_image TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id), FOREIGN KEY (application_id ) REFERENCES applications (id)); CREATE TABLE appraisals_business(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,place_of_business TEXT NOT NULL,fixed_business_assets TEXT,fixed_business_assets_amount INTEGER,running_capital TEXT,running_capital_amount INTEGER,business_expenses TEXT,business_expenses_amount INTEGER,new_required_assets TEXT NOT NULL,new_required_assets_amount INTEGER NOT NULL,place_of_buying TEXT,period_of_business INTEGER,who_are_customers TEXT,emp_before_loan INTEGER,emp_after_loan INTEGER,business_appraisal_address TEXT,description TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id), FOREIGN KEY (application_id ) REFERENCES applications (id)); CREATE TABLE members_address(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,home_address TEXT NOT NULL,business_address TEXT); CREATE TABLE members_email(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,email TEXT NOT NULL); CREATE TABLE members_phone(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,mobile TEXT NOT NULL,phone TEXT);CREATE TABLE projects_agriculture(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,owner TEXT,land_area_size INTEGER,land_area_type TEXT,village_name TEXT,uc_number TEXT,uc_name TEXT,crop_type TEXT,crops TEXT); CREATE TABLE projects_disabled(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,is_khidmat_card_holder INTEGER NOT NULL,loan_id INTEGER,disability TEXT,nature TEXT,physical_disability TEXT,visual_disability TEXT,communicative_disability TEXT,disabilities_instruments TEXT); CREATE TABLE projects_tevta(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,institute_name TEXT,type_of_diploma TEXT,duration_of_diploma TEXT,year TEXT,pbte_or_ttb TEXT,registration_no TEXT,roll_no TEXT);CREATE TABLE member_info(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,cnic_expiry_date date DEFAULT NULL,cnic_issue_date date DEFAULT NULL,mother_name TEXT);";
        $d["2"] = "DROP TABLE documents;";
        $d["3"] = "CREATE TABLE documents(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER);";
        $d["4"] = "DROP TABLE biometric;";
        $d["5"] = "CREATE TABLE biometric(id INTEGER PRIMARY KEY,file_type TEXT,file_address TEXT,parent_type TEXT,is_image_sync INTEGER,server_id INTEGER,parent_id INTEGER);";
        $d["6"] = "CREATE TABLE appraisals_housing(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,property_type TEXT NOT NULL,ownership TEXT NOT NULL,land_area INTEGER NOT NULL,residential_area INTEGER NOT NULL,living_duration INTEGER,duration_type TEXT,no_of_rooms INTEGER NOT NULL,no_of_kitchens INTEGER NOT NULL,no_of_toilets INTEGER NOT NULL,purchase_price INTEGER NOT NULL,current_price INTEGER NOT NULL,address TEXT NOT NULL,housing_appraisal_address TEXT,description TEXT,description_image TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id));";
        $d["7"] = "CREATE TABLE appraisals_agriculture(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,water_analysis INTEGER NOT NULL,soil_analysis INTEGER NOT NULL,laser_level INTEGER NOT NULL,irrigation_source TEXT NOT NULL,other_source TEXT,crop_year TEXT NOT NULL,crop_production TEXT NOT NULL,resources TEXT NOT NULL,expenses INTEGER NOT NULL,available_resources INTEGER NOT NULL,required_resources INTEGER NOT NULL,agriculture_appraisal_address TEXT,description TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id));";
        $d["8"] = "CREATE TABLE members_account(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,member_id INTEGER NOT NULL,bank_name TEXT,account_no TEXT);";
        $d["9"] = "ALTER TABLE applications ADD sub_activity TEXT;";
        $d["10"] = "ALTER TABLE applications ADD client_contribution INTEGER;";
        $d["11"] = "CREATE TABLE visits(server_id INTEGER,sync_status INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,parent_type TEXT NOT NULL, parent_id INTEGER NOT NULL,name TEXT NOT NULL, comments TEXT NOT NULL, percent TEXT NOT NULL,latitude REAL NOT NULL,longitude REAL NOT NULL,estimated_figures TEXT,estimated_start_date INTEGER,estimated_completion_time INTEGER,recommended_amount INTEGER,tranch_id INTEGER,tranch_status INTEGER);";
        $d["12"] = "CREATE TABLE civil_application(application_id INTEGER  PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,sync_status INTEGER,recommend_status INTEGER,application_data TEXT NOT NULL);";
        $d["13"] = "ALTER TABLE members_account ADD title TEXT;";
        $d["14"] = "ALTER TABLE appraisals_social ADD who_will_earn TEXT;";
        $d["15"] = "ALTER TABLE appraisals_social ADD earning_person_name TEXT;";
        $d["16"] = "ALTER TABLE appraisals_social ADD earning_person_cnic TEXT;";
        $d["17"] = "CREATE TABLE projects_psic(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,diploma_holder TEXT,institute TEXT,other_name TEXT);";
        $d["18"] = "ALTER TABLE visits ADD is_tranche INTEGER;";
        $d["19"] = "CREATE TABLE appraisals_emergency(server_id INTEGER,sync_status INTEGER,is_image_sync INTEGER,id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,house_ownership TEXT NOT NULL,total_family_members INTEGER NOT NULL,no_of_earning_hands INTEGER,ladies INTEGER NOT NULL,gents INTEGER NOT NULL,economic_dealings TEXT NOT NULL,social_behaviour TEXT NOT NULL,fatal_disease INTEGER NOT NULL Default 0,income_before_corona INTEGER,income_after_corona INTEGER,expenses_in_corona INTEGER,emergency_appraisal_address TEXT,description TEXT,latitude REAL NOT NULL,longitude REAL NOT NULL,status TEXT NOT NULL, FOREIGN KEY (application_id ) REFERENCES applications (id), FOREIGN KEY (application_id ) REFERENCES applications (id));";
        $d["20"] = "CREATE TABLE projects_sidb(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,is_khidmat_card_holder INTEGER NOT NULL,loan_id INTEGER,disability TEXT,nature TEXT,physical_disability TEXT,visual_disability TEXT,communicative_disability TEXT,disabilities_instruments TEXT);";
        $d["21"] = "ALTER TABLE visits ADD is_shifted INTEGER;";
        $d["22"] = "CREATE TABLE projects_kpp(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,training_required INTEGER NOT NULL Default 0,trainee_type TEXT,trainee_name TEXT,trainee_guardian TEXT,trainee_cnic TEXT,trainee_relation TEXT,has_sehat_card INTEGER NOT NULL Default 0,want_sehat_card INTEGER NOT NULL Default 0);";
        $d["23"] = "CREATE TABLE projects_agriculture_kpp(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,owner TEXT,land_area_size INTEGER,land_area_type TEXT,village_name TEXT,uc_number TEXT,uc_name TEXT,crop_type TEXT,crops TEXT,training_required INTEGER NOT NULL Default 0,trainee_type TEXT,trainee_name TEXT,trainee_guardian TEXT,trainee_cnic TEXT,trainee_relation TEXT,has_sehat_card INTEGER NOT NULL Default 0,want_sehat_card INTEGER NOT NULL Default 0);";
        $d["24"] = "DROP TABLE projects_agriculture_kpp;";
        $d["25"] = "CREATE TABLE projects_agriculture_kpp(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,application_id INTEGER NOT NULL,loan_id INTEGER,kpp_owner TEXT,kpp_land_area_size INTEGER,kpp_land_area_type TEXT,kpp_village_name TEXT,kpp_uc_number TEXT,kpp_uc_name TEXT,kpp_crop_type TEXT,kpp_crops TEXT,kpp_training_required INTEGER NOT NULL Default 0,kpp_trainee_type TEXT,kpp_trainee_name TEXT,kpp_trainee_guardian TEXT,kpp_trainee_cnic TEXT,kpp_trainee_relation TEXT,kpp_has_sehat_card INTEGER NOT NULL Default 0,kpp_want_sehat_card INTEGER NOT NULL Default 0);";
        $d["26"] = "ALTER TABLE members_account ADD account_type TEXT;";
        $response['data'] = $d;
        $response['info'] = [];
        return $this->sendSuccessResponse(200,$response['data'], $response['info']);
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
        $form_view = ViewFormHelper::getSectionsSchema($input['table'],'section');
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
//        $params['rbac_type'] = $this->rbac_type;
//        $params['controller'] = Yii::$app->controller->id;
//        $params['method'] = Yii::$app->controller->action->id;
//        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
//        $appraisal_list = AppraisalsHelper::getAppraisalsList($params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);
//
//        $appraisals = Appraisals::find()->all();
//        foreach ($appraisals as $appraisal)
//        {
//            $appraisal_list[$appraisal->name.'_form'] = ViewFormHelper::getSectionsSchema($appraisal->appraisal_table,'section');
//            $appraisal_list[$appraisal->name.'_data'] = DataListHelper::getDataList($appraisal->appraisal_table,$params['controller'],$params['method'],$params['rbac_type'],$params['user_id']);
//            $appraisal_list['appraisals'][]= $appraisal->appraisal_table;
//        }
//        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../../'));
//        $files = glob(Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/appraisal_json/*'); // get all file names
//        foreach($files as $file){ // iterate files
//            if(is_file($file))
//                @unlink($file); // delete file
//        }
//
//        $file_name = 'appraisals.json';
//        $file_base_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/appraisal_json/';
//
//        if (!file_exists($file_base_path)) {
//            mkdir($file_base_path, 0777, true);
//            chmod($file_base_path, 0777);
//        }
//
//
//        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/appraisal_json/' . $file_name;
//
//        file_put_contents($file_path,json_encode($appraisal_list));
//        $gz_file_name = 'appraisals_'.rand(99,999).'.json.gz';
//        $data = IMPLODE("", FILE($file_path));
//        $gzdata = GZENCODE($data, 9);
//        $fp = FOPEN($file_base_path.$gz_file_name, "w");
//        FWRITE($fp, $gzdata);
//        FCLOSE($fp);

        $gz_file_name = 'appraisals_313.json.gz';     // static formation response
        $response[] = 'appraisal_json/'.$gz_file_name;

        return $this->sendSuccessResponse(200,$response);
    }

    public function actionGroupconfig()
    {
        $response[] = 'group_size.json.gz';
        //$response[] = 'appraisal_json/appraisal_data_test_22.json.gz';
        //$response[] = 'appraisal_json/update_appraisals_new.json.gz';
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionTranches()
    {
        $response[] = 'tranches.json.gz';
        return $this->sendSuccessResponse(200,$response);
    }
}