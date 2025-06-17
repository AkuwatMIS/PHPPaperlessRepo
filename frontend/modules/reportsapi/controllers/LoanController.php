<?php

namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Parsers\ReportsParser\ApiParser;
use common\models\RandomMembers;
use common\models\search\GlobalsSearch;
use common\models\search\LoansSearch;
use common\models\search\MembersSearch;
use Symfony\Component\Yaml\Tests\A;
use Yii;
use common\models\Users;
use common\components\Helpers\ReportsHelper\StringHelper;
use yii\web\Response;

class LoanController extends \yii\web\Controller
{
    public $modelClass = 'common\models\Branches';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                //'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'de',
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionSearch()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $key    = !empty($input['key'])?($input['key']):'';
        $value     = !empty($input['value'])?($input['value']):'';
        $cnic = $sanction_no = $group_no = '';
        if($key == 'cnic'){
            $cnic = $value;
        }else if ($key == 'sanction_no'){
            $sanction_no = $value;
        }else if($key == 'group_no'){
            $group_no = $value;
        }

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $params = array();
            $searchModel = new GlobalsSearch();
            if(isset($sanction_no) && !empty($sanction_no))
            {
                $searchModel->sanction_no = $sanction_no;
                $params['GlobalSearch']['sanction_no'] = $sanction_no;
                $params['GlobalSearch']['grpno'] = '';
                $params['GlobalSearch']['borrower_cnic'] = '';
                $params['GlobalSearch']['type'] = 'sanction_no';
            } else if(isset($cnic) && !empty($cnic))
            {
                $searchModel->borrower_cnic = $cnic;
                $params['GlobalSearch']['borrower_cnic'] = $cnic;
                $params['GlobalSearch']['sanction_no'] = '';
                $params['GlobalSearch']['grpno'] = '';
                $params['GlobalSearch']['type'] = 'borrower_cnic';
            } else if(isset($group_no) && !empty($group_no))
            {
                $searchModel->grpno = $group_no;
                $params['GlobalSearch']['grpno'] = $group_no;
                $params['GlobalSearch']['sanction_no'] = '';
                $params['GlobalSearch']['borrower_cnic'] = '';
                $params['GlobalSearch']['type'] = 'grpno';
            }
            $search_member= $searchModel->searchGlobal($params);
            $borrowers = ApiParser::parseMember($search_member);
            //print_r($borrowers);
            //die("we die here");
            /*$borrowers          = Borrowers::find()->where(['cnic'=>'37102-1226474-3'])->all();
            $borrower_response = [];

            foreach($borrowers as $b){
                $borrower_response[] = $b->attributes;
            }

            $borrower = [
                $borrower_response,
                'loan' => '',
            ];*/

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Borrower Detail";
            $response['data']['borrower']   = $borrowers;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionRandom()
    {

        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);
            $params = array();
            $searchModel = new GlobalsSearch();
            $members = RandomMembers::find()->where(['deleted' => 0])->all();
            $borrowers = ApiParser::parseMemberData($members);
            /*foreach ($members as $member) {
                $params['GlobalSearch']['type'] = 'borrower_cnic';
                $params['GlobalSearch']['borrower_cnic'] = $member->cnic;

                $search_members = $searchModel->searchRandom($params);

                $borrowers[] = ApiParser::parseMember($search_members);
            }*/

            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Borrowers Detail";
            $response['data']['borrower']   = $borrowers;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionLedger()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $id     = !empty($input['loan_id'])?($input['loan_id']):'';

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $params['LoansSearch']['id'] = $id;

            $searchModel = new LoansSearch();
            $search_loan= $searchModel->searchLedger($params);

            //apply date sorting
            if(!empty($search_loan)){

                $schedules = $search_loan->schedules;
                $recoveries = $search_loan->recoveries;
                foreach ($schedules as $key => $part) {

                    $sort[$key] = $part['due_date'];
                }
                if(!empty($schedules)){
                    array_multisort($sort, SORT_ASC, $schedules);
                }

                $borrowers = ApiParser::parseMemberLedger($search_loan->application);
                $ledger = ApiParser::parseLedger($schedules,$search_loan->loan_amount);
                /*foreach ($recoveries as $r){
                    print_r($r->recv_date.'\n');
                }*/
                //print_r($borrowers);
                //die("we die here");
                $borrowers['loan'] = ApiParser::parseLoanLedger($search_loan);
                $borrowers['ledger'] = $ledger;
                $borrowers['total'] = ApiParser::parseLoanTotal($search_loan,$recoveries);
            }else{
                $borrowers = 'Record not found';
            }



            $response['meta']['success']    = true;
            $response['meta']['code']       = 200;
            $response['data']['message']    = "Get Borrower Ledger Detail";
            $response['data']['borrower']   = $borrowers;

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

    public function actionGetLoanRecovery()
    {
        $input   = Yii::$app->request->getBodyParams();
        $headers        = Yii::$app->request->headers;
        $access_token   = $headers->get('access_token');
        $version_code   = $headers->get('version_code');
        $response       = [];


        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        $key    = !empty($input['type'])?($input['type']):'';
        $value     = !empty($input['value'])?($input['value']):'';
        $cnic = $sanction_no = '';
        if($key == 'cnic'){
            $cnic = $value;
        }else if ($key == 'sanction_no'){
            $sanction_no = $value;
        }else{
            $response['meta']['success']    = false;
            $response['meta']['code']       = 600;
            $response['data']['message']    = "Invalid Data Format";
            return $response;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->one();
        $borrowers = array();
        if ($user) {
            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $user->post_token = StringHelper::getRandom(40);
            //$user->post_token_created_date = date('Y-m-d H:i:s');
            if($user->save()){
                $params['LoansSearch']['sanction_no'] = isset($sanction_no) ? $sanction_no : '';
                $params['LoansSearch']['member_cnic'] = isset($cnic) ? $cnic : '';
                /*print_r($params);
                die();*/
                $searchModel = new LoansSearch();
                $search_loans= $searchModel->searchRecoveriesLedger($params);
                /*print_r($search_loans);
                die();*/
                //apply date sorting
                if(!empty($search_loans)){

                    foreach ($search_loans as $key => $search_loan){
                        $schedules = array_slice($search_loan->schedules, -2, 2, true);
                        /*print_r($schedules);
                        die();*/
                        //$data_borrower  = ApiParser::parseBorrowerRecoveries($search_loan->borrower);
                        $data_loan = ApiParser::parseLoanRecoveries($search_loan,$user);
                        //$data_ledger = ApiParser::parseRecoveriesLedger($schedules,$search_loan->amountapproved);
                        $one  = [
                            //'loan'=>$data_loan
                            'loan' => $data_loan,

                        ];
                        $borrowers[] = $one;

                        /*$borrowers[$key]['loan'] = ApiParser::parseLoanRecoveries($search_loan);
                        $borrowers[$key]['ledger'] = ApiParser::parseRecoveriesLedger($search_loan->schedules,$search_loan->amountapproved);*/
                        //  break;
                    }
                    $response['meta']['success']    = true;
                    $response['meta']['code']       = 200;
                    $response['data']['message']    = "Get Borrower Ledger Detail";
                    $response['data']['post_token']    = $user->post_token;
                    $response['data']['details']  = $borrowers;
                }else{
                    $response['meta']['success']    = false;
                    $response['meta']['code']       = 404;
                    $response['data']['message']    = "No Active Loan Found";
                }

            }else{
                $response['meta']['success'] = false;
                $response['meta']['code'] = 500;
                $response['data']['errors']['message'] = 'Access Denied, please try again';
                $response['data']['errors']['type'] = "";
            }

        }else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }

        return $response;
    }

}

