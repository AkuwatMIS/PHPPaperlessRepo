<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\JsonHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\ImageHelper;
use common\models\Applications;
use common\models\Areas;
use common\models\Branches;
use common\models\Donations;
use common\models\Loans;
use common\models\Members;
use common\models\MemberInfo;
use common\models\MembersPhone;
use common\models\Model;
use common\models\Projects;
use common\models\Recoveries;
use common\models\RecoveryErrors;
use common\models\Regions;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Response;


class CihController extends RestController
{
    public $rbac_type = 'api';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }


    public function actionSearchMember()
    {
        $paramsCnic = isset($this->request['cnic']) ? $this->request['cnic'] : 0;

        if ($paramsCnic != 0) {
            $member = Members::find()->where(['cnic' => $paramsCnic])->andWhere(['deleted' => 0])
                ->select(['id', 'full_name', 'parentage', 'cnic', 'status'])
                ->one();
            if (!isset($member) && empty($member) && $member == null) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Member Not Found against CNIC ' . $paramsCnic,
                    'status_code' => 201
                ];
                return JsonHelper::asJson($response);
            } else {
                $application = Applications::find()->where(['member_id' => $member->id])
                    ->andWhere(['status' => 'pending'])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['id' => 'SORT_DESC'])
                    ->one();

                if (!isset($application) && empty($application) && $application == null) {
                    $membersPhone = MembersPhone::find()->where(['member_id' => $member->id])
                        ->andWhere(['is_current' => 1])
                        ->select(['id', 'phone'])
                        ->orderBy(['id' => 'SORT_DESC'])
                        ->one();

                    $response['meta'] = [
                        'error' => false,
                        'message' => 'success!',
                        'status_code' => 200
                    ];
                    $response['data']['id'] = $member->id;
                    $response['data']['name'] = $member->full_name;
                    $response['data']['parentage'] = $member->parentage;
                    $response['data']['cnic'] = $member->cnic;
                    $response['data']['status'] = $member->status;
                    if (!empty($membersPhone) && $membersPhone != null) {
                        $phone = str_replace("92", "0", $membersPhone->phone);
                        $response['data']['phone'] = $phone;
                    } else {
                        $response['data']['phone'] = 00000000000;
                    }

                    return JsonHelper::asJson($response);
                } else {
                    $response['meta'] = [
                        'error' => true,
                        'message' => 'Application against this CNIC is already in-process!',
                        'status_code' => 201
                    ];
                    return JsonHelper::asJson($response);
                }
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'CNIC Input required!',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }

    }

    public function actionBranchTakaful()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);


        $current_from_date = date('Y-m-1');
        $current_to_date = date('Y-m-t');
        $fromDate = strtotime($current_from_date);
        $toDate = strtotime($current_to_date);

        $paramsBid = isset($this->request['branch_id']) ? $this->request['branch_id'] : 0;

        if ($paramsBid != 0) {

            $sql = "SELECT
                loans.id AS member_sync_id,
                members.full_name,
                members.parentage AS parentage,
                members.cnic AS cnic,
                (
                SELECT
                    phone
                FROM
                    members_phone
                WHERE
                    is_current = 1 AND member_id = members.id AND phone_type = \"Mobile\"
                ORDER BY
                    id
                DESC
            LIMIT 1
            ) AS `phone`,
             `loans`.`project_id`,
              `applications`.`application_no`,
               `groups`.`grp_no`,
               (
            SELECT CODE
            FROM
                branches
            WHERE
                id = applications.branch_id
            ) AS `branch_code`,
            `loans`.`sanction_no`,
            `applications`.`region_id`,
            `applications`.`area_id`,
                (
                SELECT
                    code
                FROM
                    branches
                WHERE
                    id = applications.branch_id
                
            ) AS `branch_code`,
            `applications`.`branch_id`,
            `loans`.`loan_amount`,
            (`loans`.`loan_amount`)/100 as takaf_due_amnt,
            `loan_tranches`.`cheque_date` as 'due_date'
            FROM
                `loan_tranches`
            INNER JOIN
                `loans`
            ON
                `loan_tranches`.`loan_id` = `loans`.`id`
            INNER JOIN
                `applications`
            ON
                `loans`.`application_id` = `applications`.`id`
            INNER JOIN
                `members`
            ON
                `applications`.`member_id` = `members`.`id`
            INNER JOIN
                `groups`
            ON
                `applications`.`group_id` = `groups`.`id`
            LEFT JOIN
                `teams`
            ON
                `applications`.`team_id` = `teams`.`id`
            WHERE
                (
                    (`loans`.`branch_id`=$paramsBid) AND(`loans`.`deleted` = 0)
                ) AND(`loans`.`status` = \"pending\") AND(`loan_tranches`.`status` = 4) 
                AND (`loans`.`date_disbursed`=0) AND (`loans`.`disbursement_id`=0) 
                AND loans.project_id NOT IN(4, 52, 61, 62, 64, 67, 75, 76, 77, 78, 79, 83, 90, 103, 110, 3, 17,132)";
            $query = Yii::$app->db->createCommand($sql);
            $branchData = $query->queryAll();

            $sqlData = "SELECT
                loans.id AS member_sync_id,
                members.full_name,
                members.parentage AS parentage,
                members.cnic AS cnic,
                (
                SELECT
                    phone
                FROM
                    members_phone
                WHERE
                    is_current = 1 AND member_id = members.id AND phone_type = \"Mobile\"
                            ORDER BY
                                id
                            DESC
                        LIMIT 1
                        ) AS `phone`, `loans`.`project_id`, `applications`.`application_no`, `groups`.`grp_no`,(
                        SELECT CODE
                        FROM
                            branches
                        WHERE
                            id = applications.branch_id
                        ) AS `branch_code`,
                        `loans`.`sanction_no`,
                        `applications`.`region_id`,
                        `applications`.`area_id`,
                            (
                            SELECT
                                code
                            FROM
                                branches
                            WHERE
                                id = applications.branch_id
                            
                        ) AS `branch_code`,
                        `applications`.`branch_id`,
                        `loans`.`loan_amount`,
                        (`loans`.`loan_amount`)/100 as takaf_due_amnt,
                        `loan_tranches`.`cheque_date` as 'due_date'
                        FROM
                            `loan_tranches`
                        INNER JOIN
                            `loans`
                        ON
                            `loan_tranches`.`loan_id` = `loans`.`id`
                        INNER JOIN
                            `applications`
                        ON
                            `loans`.`application_id` = `applications`.`id`
                        INNER JOIN
                            `members`
                        ON
                            `applications`.`member_id` = `members`.`id`
                        INNER JOIN
                            `groups`
                        ON
                            `applications`.`group_id` = `groups`.`id`
                        LEFT JOIN
                            `teams`
                        ON
                            `applications`.`team_id` = `teams`.`id`
                        WHERE
                            (  (`loans`.`branch_id`=$paramsBid) AND(`loans`.`deleted` = 0)
                            ) AND (`loans`.`date_disbursed`BETWEEN $fromDate AND $toDate) 
                            AND loans.project_id NOT IN(4, 52, 61, 62, 64, 67, 75, 76, 77, 78, 79, 83, 90, 103, 110, 3, 17,132)";
            $queryData = Yii::$app->db->createCommand($sqlData);
            $branchDataSanction = $queryData->queryAll();

            $sqlKpData = "SELECT
                loans.id AS member_sync_id,
                members.full_name,
                members.parentage AS parentage,
                members.cnic AS cnic,
                (
                SELECT
                    phone
                FROM
                    members_phone
                WHERE
                    is_current = 1 AND member_id = members.id AND phone_type = \"Mobile\"
                            ORDER BY
                                id
                            DESC
                        LIMIT 1
                        ) AS `phone`, `applications`.`project_id`, `applications`.`application_no`, `groups`.`grp_no`,(
                        SELECT CODE
                        FROM
                            branches
                        WHERE
                            id = applications.branch_id
                        ) AS `branch_code`,
                        `loans`.`sanction_no`,
                        `applications`.`region_id`,
                        `applications`.`area_id`,
                            (
                            SELECT
                                code
                            FROM
                                branches
                            WHERE
                                id = applications.branch_id
                            
                        ) AS `branch_code`,
                        `applications`.`branch_id`,
                        `takaful_due`.`olp` as loan_amount,
                        (`takaful_due`.`takaful_amnt`) as takaf_due_amnt,
                        `takaful_due`.`overdue_date` as 'due_date'
                        FROM
                            `takaful_due`
                        INNER JOIN
                            `loans`
                        ON
                            `takaful_due`.`loan_id` = `loans`.`id`
                        INNER JOIN
                            `applications`
                        ON
                            `loans`.`application_id` = `applications`.`id`
                        INNER JOIN
                            `members`
                        ON
                            `applications`.`member_id` = `members`.`id`
                        INNER JOIN
                            `groups`
                        ON
                            `applications`.`group_id` = `groups`.`id`
                        LEFT JOIN
                            `teams`
                        ON
                            `applications`.`team_id` = `teams`.`id`
                        WHERE
                            (`loans`.`branch_id`=$paramsBid) AND(`loans`.`deleted` = 0) AND `takaful_due`.`status`=0";
            $sqlKpData = Yii::$app->db->createCommand($sqlKpData);
            $kpDataSanction = $sqlKpData->queryAll();


            if (!empty($branchDataSanction) && !empty($kpDataSanction)) {
                $data = array_merge($branchDataSanction, $branchData);
                $data = array_merge($data, $kpDataSanction);
            } elseif (!empty($branchDataSanction) && empty($kpDataSanction)) {
                $data = array_merge($branchData, $branchDataSanction);
            } elseif (!empty($kpDataSanction) && empty($branchDataSanction)) {
                $data = array_merge($branchData, $kpDataSanction);
            } else {
                $data = $branchData;
            }

            if (!isset($data) && empty($data) && $data == null) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'No Data Found',
                    'status_code' => 201
                ];
                return JsonHelper::asJson($response);
            } else {
                $response['meta'] = [
                    'error' => false,
                    'message' => 'Data Found',
                    'status_code' => 200
                ];
                $response['data'] = $data;
                return JsonHelper::asJson($response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }

    }

    public function actionBranchTakafulLoan()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);


        $current_from_date = date('Y-m-1');
        $current_to_date = date('Y-m-t');
        $fromDate = strtotime($current_from_date);
        $toDate = strtotime($current_to_date);

        $paramsBid = isset($this->request['branch_id']) ? $this->request['branch_id'] : 0;

        if ($paramsBid != 0) {

            $sql = "SELECT
                loans.id AS member_sync_id,
                members.full_name,
                members.parentage AS parentage,
                members.cnic AS cnic,
                (
                SELECT
                    phone
                FROM
                    members_phone
                WHERE
                    is_current = 1 AND member_id = members.id AND phone_type = \"Mobile\"
                ORDER BY
                    id
                DESC
            LIMIT 1
            ) AS `phone`, `loans`.`project_id`, `applications`.`application_no`, `groups`.`grp_no`,(
            SELECT CODE
            FROM
                branches
            WHERE
                id = applications.branch_id
            ) AS `branch_code`,
            `loans`.`sanction_no`,
            `applications`.`region_id`,
            `applications`.`area_id`,
                (
                SELECT
                    code
                FROM
                    branches
                WHERE
                    id = applications.branch_id
                
            ) AS `branch_code`,
            `applications`.`branch_id`,
            `loans`.`loan_amount`,
            `loans`.`disbursed_amount`,
            `loans`.`inst_amnt`
            FROM
                `loan_tranches`
            INNER JOIN
                `loans`
            ON
                `loan_tranches`.`loan_id` = `loans`.`id`
            INNER JOIN
                `applications`
            ON
                `loans`.`application_id` = `applications`.`id`
            INNER JOIN
                `members`
            ON
                `applications`.`member_id` = `members`.`id`
            INNER JOIN
                `groups`
            ON
                `applications`.`group_id` = `groups`.`id`
            LEFT JOIN
                `teams`
            ON
                `applications`.`team_id` = `teams`.`id`
            WHERE
                (
                    (`loans`.`branch_id`=$paramsBid) AND(`loans`.`deleted` = 0)
                ) AND(`loans`.`status` = \"pending\") AND(`loan_tranches`.`status` = 4) AND (`loans`.`date_disbursed`=0) AND (`loans`.`disbursement_id`=0) AND loans.project_id NOT IN(4, 52, 61, 62, 64, 67, 75, 76)";
            $query = Yii::$app->db->createCommand($sql);
            $branchData = $query->queryAll();

            if (!isset($branchData) && empty($branchData) && $branchData == null) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'No Data Found',
                    'status_code' => 201
                ];
                return JsonHelper::asJson($response);
            } else {
                $response['meta'] = [
                    'error' => false,
                    'message' => 'Data Found',
                    'status_code' => 200
                ];
                $response['data'] = $branchData;
                return JsonHelper::asJson($response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }

    }

    public function actionGetActiveLoanByBranch()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $paramsBid = isset($this->request['branch_id']) ? $this->request['branch_id'] : 0;
        $paramsDate = isset($this->request['report_date']) ? $this->request['report_date'] : 0;

       // $schedule_date = strtotime(date('Y-m-d-23:59', (strtotime($paramsDate . '+ 9 days'))));
        $schedule_date = strtotime(date('Y-m-10-00:00', (strtotime($paramsDate))));
        $disbursed_date = strtotime(date("Y-m-d-23:59", $schedule_date));
        $recovery_date = strtotime(date('Y-m-t-23:59', (strtotime($paramsDate . '- 1 months'))));
        $contactType = 'Mobile';

        if ($paramsBid != 0) {

            $sql = "SELECT
                    loans.id as member_sync_id,
                    applications.application_no,
                    loans.sanction_no,
                    loans.inst_amnt,
                    loans.project_id,
                    members.full_name AS full_name,
                    members.cnic AS cnic,
                    members.parentage AS parentage,
                    loans.loan_amount,
                    @amountapproved:=loans.disbursed_amount AS disbursed_amount,
                    applications.region_id,
                    applications.area_id,
                    applications.branch_id,
                    FROM_UNIXTIME(loans.date_disbursed) AS date_disbursed,
                    (
                    SELECT
                        code
                    FROM
                        branches
                    WHERE
                        id = applications.branch_id

                ) AS branch_code,
                    (
                    SELECT
                        phone
                    FROM
                        members_phone
                    WHERE
                        is_current = 1 AND member_id = members.id AND phone_type='Mobile'
                    ORDER BY
                        id
                    DESC
                LIMIT 1
                ) AS phone, 
                groups.grp_no AS grp_no,
                   @recovery:=(select COALESCE(sum(amount),0) from recoveries where loan_id=loans.id and deleted=0 and receive_date <= $recovery_date) as recovery,
                   @due_amount_var:=((select COALESCE(sum(schdl_amnt),0) from schedules where loan_id=loans.id and due_date <= $schedule_date)-@recovery) 'actual_due_amount',
                    
                   @receive_charges_amount:=(select COALESCE(sum(charges_amount),0) from recoveries where loan_id=loans.id and deleted=0 and receive_date <= $recovery_date) as 'receive_charges_amount',
                   @due_charges_amount:=((select COALESCE(sum(charges_schdl_amount),0) from schedules where loan_id=loans.id and due_date <= $schedule_date group by loan_id)-@receive_charges_amount) 'actual_charges_amount_due',

                   @receive_sale_tax:=(select COALESCE(sum(credit_tax),0) from recoveries where loan_id=loans.id and deleted=0 and receive_date <=$recovery_date) as 'receive_sale_tax',
                   @due_amount_tax:=((select COALESCE(sum(charges_schdl_amnt_tax),0) from schedules where loan_id=loans.id and due_date <= $schedule_date group by loan_id )-@receive_sale_tax) 'actual_sale_tax_due',
                   @balance:=(loans.disbursed_amount-@recovery) as olp,
           @due_1 := CASE
                       WHEN @due_amount_var > sch.schdl_amnt THEN @due_amount_var
                       WHEN sch.schdl_amnt IS NULL THEN @due_amount_var
                       WHEN @balance < sch.schdl_amnt THEN @balance
                       WHEN @balance > sch.schdl_amnt THEN sch.schdl_amnt
                       WHEN @due_amount_var < 0 THEN sch.schdl_amnt
                       ELSE sch.schdl_amnt
                    END AS 'due_amount',
            
                    @due_2 := CASE
                       WHEN @due_charges_amount > sch.charges_schdl_amount THEN @due_charges_amount
                       WHEN sch.charges_schdl_amount IS NULL THEN @due_charges_amount
                       ELSE sch.charges_schdl_amount
                       END AS due_charges_amount,
            
                   @due_3 := CASE
                       WHEN @due_amount_tax > sch.charges_schdl_amnt_tax THEN @due_amount_tax
                       WHEN sch.charges_schdl_amnt_tax IS NULL THEN @due_amount_tax
                       ELSE sch.charges_schdl_amnt_tax
                       END AS due_amount_tax,
                   
                   @due_1+@due_2+@due_3 as due,
                
                teams.name As team
                FROM
                    loan_tranches
                LEFT JOIN
                    loans
                ON
                    loan_tranches.loan_id = loans.id
                LEFT JOIN
                    applications
                ON
                    loans.application_id = applications.id
                LEFT JOIN
                    members
                ON
                    applications.member_id = members.id
                LEFT JOIN
                    groups
                ON
                    applications.group_id = groups.id
                LEFT JOIN
                    teams
                ON
                    applications.team_id = teams.id
                LEFT JOIN schedules sch ON sch.loan_id = loans.id and sch.due_date= $schedule_date
                where
                    loans.status in ('collected','loan completed')
                    and
                    loans.date_disbursed <= $disbursed_date
                    AND loans.branch_id =$paramsBid
                    group by loans.sanction_no
                    having olp>0 AND (@due_1 > 0 OR due_amount > 0) ";

            try {
                $queryData = Yii::$app->db->createCommand($sql);
                $branchActiveLoans = $queryData->queryAll();
            } catch (\yii\db\Exception $e) {
                $error = $e->getMessage(); // Log or display the error
                return JsonHelper::asJson($error);
            }
            if (empty($branchActiveLoans) && $branchActiveLoans == null) {
                $response['data'] = [];
                return JsonHelper::asJson($response);
            } else {
                $response['data'] = $branchActiveLoans;
                return JsonHelper::asJson($response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            $response['data'] = [];
            return JsonHelper::asJson($response);
        }
    }

    public function actionGetActiveLoanBySanctionArray()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 1000);

        $schedule_date = strtotime(date('Y-m-d-23:59', (strtotime(date('Y-m-01-23:59') . '+ 9 days'))));
        $recovery_date = strtotime(date('Y-m-t-23:59', (strtotime(date('Y-m-01-23:59') . '- 1 months'))));
        
        $sanction_Array = [];
        foreach ($this->request as $key => $sanction) {
            $sanction_Array[$key] = "'$sanction'";
        }
        $sanction_Array = implode(",", $sanction_Array);

        if (!empty($sanction_Array)) {
            $sql = "SELECT
                    `loans`.`id` as `member_sync_id`,
                    `applications`.`application_no`,
                    `loans`.`sanction_no`,
                    `loans`.`inst_amnt`,
                    `loans`.`project_id`,
                    `members`.`full_name` AS `full_name`,
                    `members`.`cnic` AS `cnic`,
                    `members`.`parentage` AS `parentage`,
                    `loans`.`loan_amount`,
                    @amountapproved:=`loans`.`disbursed_amount` AS `disbursed_amount`,
                    `applications`.`region_id`,
                    `applications`.`area_id`,
                    `applications`.`branch_id`,
                    FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date_disbursed`,
                    (
                    SELECT
                        code
                    FROM
                        branches
                    WHERE
                        id = applications.branch_id

                ) AS `branch_code`,
                    (
                    SELECT
                        phone
                    FROM
                        members_phone
                    WHERE
                        is_current = 1 AND member_id = members.id AND phone_type='Mobile'
                    ORDER BY
                        id
                    DESC
                LIMIT 1
                ) AS `phone`, `groups`.`grp_no` AS `grp_no`, @schdl_till_current_month :=(
                SELECT
                  (sum(schedules.schdl_amnt)+sum(schedules.charges_schdl_amount))
                FROM
                    schedules
                WHERE
                    (
                        schedules.loan_id = loans.id and schedules.due_date<=$schedule_date
                    )
                ) AS `schdl_till_current_month`,
                @recovery :=(
                SELECT 
                   coalesce((sum(recoveries.amount)+sum(recoveries.charges_amount)),0)
                FROM
                    recoveries
                WHERE
                    ( 
                        recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date<=$recovery_date
                    )
                ) AS `recovery`,
                (
                   IF(( @schdl_till_current_month - @recovery )>0,
                      ( IF(( @schdl_till_current_month - @recovery )<(`loans`.`inst_amnt`) AND  ((@amountapproved - @recovery)>(`loans`.`inst_amnt`)), (`loans`.`inst_amnt`) , (@schdl_till_current_month - @recovery) ) ), `loans`.`inst_amnt`) ) AS `due`,
                (
                    (
                        (@amountapproved-@recovery)
                    )
                ) AS `olp`,
                `teams`.`name` As team
                FROM
                    `loan_tranches`
                LEFT JOIN
                    `loans`
                ON
                    `loan_tranches`.`loan_id` = `loans`.`id`
                LEFT JOIN
                    `applications`
                ON
                    `loans`.`application_id` = `applications`.`id`
                LEFT JOIN
                    `members`
                ON
                    `applications`.`member_id` = `members`.`id`
                LEFT JOIN
                    `groups`
                ON
                    `applications`.`group_id` = `groups`.`id`
                LEFT JOIN
                    `teams`
                ON
                    `applications`.`team_id` = `teams`.`id`
                WHERE (`loans`.`sanction_no` IN ($sanction_Array))";

            $queryData = Yii::$app->db->createCommand($sql);
            $branchActiveLoans = $queryData->queryAll();

            if (empty($branchActiveLoans) && $branchActiveLoans == null) {
                $response['data'] = [];
                return JsonHelper::asJson($response);
            } else {
                $response['data'] = $branchActiveLoans;
                return JsonHelper::asJson($response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            $response['data'] = [];
            return JsonHelper::asJson($response);
        }
    }

    protected function findModel($id)
    {
        if (($model = Members::findOne(['id' => $id, 'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }

    public function actionPostRecovery()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $modelImport = DynamicModel::validateData($this->request, [
            [['sanction_no'], 'required'],
            [['bank_branch_name'], 'required'],
            [['bank_branch_code'], 'required'],
            [['receive_date'], 'required'],
            [['receipt_no'], 'required'],
            [['credit'], 'required']
        ]);

        if (!$modelImport) {
            $response['meta'] = [
                'error' => true,
                'message' => 'Required Inputs not found!',
                'status_code' => 201
            ];

            $response['data'] = [
                'cih_id' => $this->request['cih_id'],
                'is_sync' => '0',
                'error_message' => 'Required Inputs not found!',
                'error' => ''
            ];
            return JsonHelper::asJson($response);
        }

        try {
            $current_date = strtotime("now");
            $last_months = strtotime('now first day of last month');

            $model = new Recoveries();
            $model->receipt_no = trim($this->request['receipt_no']);
            $model->receive_date = strtotime($this->request['receive_date']);
            $model->amount = $this->request['credit'];
            $model->created_by = '1';
            $model->source = 'cih';
            $model->sanction_no = $this->request['sanction_no'];

            if (($model->receive_date > $current_date) || ($model->receive_date <= $last_months)) {
                if (empty($model->sanction_no)) {
                    $model->addError('sanction_no', 'Sanction No not Exists.');
                }

                if ($model->receive_date > $current_date) {
                    $model->addError('receive_date', 'recovery date is in future date.');
                }
                if ($model->receive_date <= $last_months) {
                    $model->addError('receive_date', 'recovery date is in previous month.');
                }

                $error_model = new RecoveryErrors();
                $error_model->sanction_no = $model->sanction_no;
                $error_model->recv_date = $model->receive_date;
                $error_model->receipt_no = 'abc';
                $error_model->credit = isset($model->amount) ? $model->amount : 0;
                $error_model->bank_branch_name = $this->request['bank_branch_name'];
                $error_model->bank_branch_code = $this->request['bank_branch_code'];
                $error_model->source = 'cih';
                if (isset($model->loan->balance)) {
                    $error_model->balance = $model->loan->balance;
                }
                if (isset($model->branch_id)) {
                    $error_model->branch_id = $model->branch_id;
                }
                if (isset($model->area_id)) {
                    $error_model->area_id = $model->area_id;
                }
                if (isset($model->region_id)) {
                    $error_model->region_id = $model->region_id;
                }
                $error_model->recovery_files_id = 0;
                $error_model->status = '0';
                $error_model->error_description = json_encode($model->getErrors());
                $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                $error_model->created_by = '1';
                $error_model->assigned_to = '1';
                $error_model->save();
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Error transaction!',
                    'status_code' => 200
                ];
                $response['data'] = [
                    'cih_id' => $this->request['cih_id'],
                    'is_sync' => '0',
                    'message' => 'Mis Error recording failed against recovery date!',
                    'error' => $model->getErrors()
                ];
            } else {
                if (!$model->save()) {
                    if (empty($model->sanction_no)) {
                        $model->addError('sanction_no', 'This CNIC No not Exists.');
                    }
                    $error_model = new RecoveryErrors();
                    $error_model->sanction_no = $model->sanction_no;
                    $error_model->recv_date = $model->receive_date;
                    $error_model->receipt_no = 'abc';
                    $error_model->credit = isset($model->amount) ? $model->amount : 0;
                    $error_model->bank_branch_name = $this->request['bank_branch_name'];
                    $error_model->bank_branch_code = $this->request['bank_branch_code'];
                    $error_model->source = 'cih';

                    if (isset($model->loan->balance)) {
                        $error_model->balance = $model->loan->balance;
                    }
                    if (isset($model->branch_id)) {
                        $error_model->branch_id = $model->branch_id;
                    }
                    if (isset($model->area_id)) {
                        $error_model->area_id = $model->area_id;
                    }
                    if (isset($model->region_id)) {
                        $error_model->region_id = $model->region_id;
                    }
                    $error_model->recovery_files_id = 0;
                    $error_model->status = '0';
                    $error_model->error_description = json_encode($model->getErrors());
                    $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                    $error_model->created_by = '1';
                    $error_model->assigned_to = '1';
                    $error_model->save();
                    $response['meta'] = [
                        'error' => true,
                        'message' => 'Error transaction!',
                        'status_code' => 200
                    ];
                    $response['data'] = [
                        'cih_id' => $this->request['cih_id'],
                        'is_sync' => '0',
                        'error_message' => 'Mis Error recorded against recovery date!',
                        'error' => $model->getErrors()
                    ];

                } else {
                    $response['meta'] = [
                        'error' => false,
                        'message' => 'Transaction successful!',
                        'status_code' => 200
                    ];
                    $response['data'] = [
                        'cih_id' => $this->request['cih_id'],
                        'is_sync' => '1'
                    ];
                    if (in_array($model->project_id, [52, 61, 62, 64, 67, 76, 77])) {
                        SmsHelper::SmsLogs('recovery', $model);
                    }
                }

            }
            return JsonHelper::asJson($response);

        } catch (\Exception $e) {
            $response['meta'] = [
                'error' => true,
                'message' => 'Required Inputs not found!',
                'status_code' => 201
            ];

            $response['data'] = [
                'cih_id' => $this->request['cih_id'],
                'is_sync' => '0',
                'error_message' => 'Required Inputs not found!',
                'error' => $e->getMessage()
            ];
            return JsonHelper::asJson($response);
        }

    }

    public function actionPostRecoveryBulk()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 700);


        $requestInput = json_decode(urldecode($this->request['data']));

        $errorResult = [];
        $successResult = [];

        foreach ($requestInput as $key => $input) {

            try {
                $current_date = strtotime("now");
                $last_months = strtotime('now first day of last month');

                $model = new Recoveries();
                $model->receipt_no = trim($input->receipt_no);
                $model->receive_date = strtotime($input->receive_date);
                $model->amount = $input->credit;
                $model->created_by = '1';
                $model->source = 'cih';
                $model->sanction_no = $input->sanction_no;

                if (($model->receive_date > $current_date) || ($model->receive_date <= $last_months)) {
                    if (empty($model->sanction_no)) {
                        $model->addError('sanction_no', 'Sanction No not Exists.');
                    }

                    if ($model->receive_date > $current_date) {
                        $model->addError('receive_date', 'recovery date is in future date.');
                    }
                    if ($model->receive_date <= $last_months) {
                        $model->addError('receive_date', 'recovery date is in previous month.');
                    }

                    $error_model = new RecoveryErrors();
                    $error_model->sanction_no = $model->sanction_no;
                    $error_model->recv_date = $model->receive_date;
                    $error_model->receipt_no = 'abc';
                    $error_model->credit = isset($model->amount) ? $model->amount : 0;
                    $error_model->bank_branch_name = $input->bank_branch_name;
                    $error_model->bank_branch_code = $input->bank_branch_code;
                    $error_model->source = 'cih';
                    if (isset($model->loan->balance)) {
                        $error_model->balance = $model->loan->balance;
                    }
                    if (isset($model->branch_id)) {
                        $error_model->branch_id = $model->branch_id;
                    }
                    if (isset($model->area_id)) {
                        $error_model->area_id = $model->area_id;
                    }
                    if (isset($model->region_id)) {
                        $error_model->region_id = $model->region_id;
                    }
                    $error_model->recovery_files_id = 0;
                    $error_model->status = '0';
                    $error_model->error_description = json_encode($model->getErrors());
                    $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                    $error_model->created_by = '1';
                    $error_model->assigned_to = '1';
                    $error_model->save();

                    $errorResult[$key]['cih_id'] = $input->cih_id;
                    $errorResult[$key]['teller_id'] = $input->teller_id;
                    $errorResult[$key]['receive_date'] = $input->receive_date;
                    $errorResult[$key]['is_sync'] = 0;
                    $errorResult[$key]['message'] = $model->getErrors();
                } else {
                    if (!$model->save()) {
                        if (empty($model->sanction_no)) {
                            $model->addError('sanction_no', 'This CNIC No not Exists.');
                        }
                        $error_model = new RecoveryErrors();
                        $error_model->sanction_no = $model->sanction_no;
                        $error_model->recv_date = $model->receive_date;
                        $error_model->receipt_no = 'abc';
                        $error_model->credit = isset($model->amount) ? $model->amount : 0;
                        $error_model->bank_branch_name = $input->bank_branch_name;
                        $error_model->bank_branch_code = $input->bank_branch_code;
                        $error_model->source = 'cih';

                        if (isset($model->loan->balance)) {
                            $error_model->balance = $model->loan->balance;
                        }
                        if (isset($model->branch_id)) {
                            $error_model->branch_id = $model->branch_id;
                        }
                        if (isset($model->area_id)) {
                            $error_model->area_id = $model->area_id;
                        }
                        if (isset($model->region_id)) {
                            $error_model->region_id = $model->region_id;
                        }
                        $error_model->recovery_files_id = 0;
                        $error_model->status = '0';
                        $error_model->error_description = json_encode($model->getErrors());
                        $error_model->created_at = strtotime(date('Y-m-d H:i:s'));
                        $error_model->created_by = '1';
                        $error_model->assigned_to = '1';
                        $error_model->save();

                        $errorResult[$key]['cih_id'] = $input->cih_id;
                        $errorResult[$key]['teller_id'] = $input->teller_id;
                        $errorResult[$key]['receive_date'] = $input->receive_date;
                        $errorResult[$key]['is_sync'] = 0;
                        $errorResult[$key]['message'] = $model->getErrors();

                    } else {
                        $response['meta'] = [
                            'error' => false,
                            'message' => 'Transaction successful!',
                            'status_code' => 200
                        ];

                        $successResult[$key]['cih_id'] = $input->cih_id;
                        $successResult[$key]['is_sync'] = '1';
                        $successResult[$key]['message'] = 'Successfully recorded';

                        if (in_array($model->project_id, [52, 61, 62, 64, 67, 76, 77])) {
                            SmsHelper::SmsLogs('recovery', $model);
                        }
                    }

                }

            } catch (\Exception $e) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Error inputs!',
                    'status_code' => 201
                ];

                $response['data'] = [
                    'cih_id' => $input->cih_id,
                    'is_sync' => '0',
                    'error_message' => 'Required Inputs not found!',
                    'error' => $e->getMessage()
                ];
                print_r(JsonHelper::asJson($response));
                die();
            }
        }

        $response['meta'] = [
            'error' => false,
            'status_code' => 200
        ];
        $response['data'] = [
            'success' => $successResult,
            'error' => $errorResult
        ];
        return JsonHelper::asJson($response);

    }

    public function actionPostMdp()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $sanction_no = isset($this->request['sanction_no']) ? $this->request['sanction_no'] : 'NA';
        $branch_name = isset($this->request['branchName']) ? $this->request['branchName'] : 'NA';
        $branch_code = isset($this->request['branchCode']) ? $this->request['branchCode'] : 'NA';
        $receive_date = isset($this->request['receive_date']) ? $this->request['receive_date'] : 'NA';
        $receipt_no = isset($this->request['receipt_no']) ? $this->request['receipt_no'] : 'NA';
        $amount = isset($this->request['amount']) ? $this->request['amount'] : 'NA';
        $cih_id = isset($this->request['cih_id']) ? $this->request['cih_id'] : 'NA';

        if ($branch_name != 'NA' && $branch_code != 'NA' && $sanction_no != 'NA' && $receive_date != 'NA' && $receipt_no != 'NA' && $amount != 'NA') {
            $DonationsArray['Donations'] = [
                [
                    'sanction_no' => $sanction_no,
                    'branch_name' => $branch_name,
                    'branch_code' => $branch_code,
                    'receive_date' => $receive_date,
                    'receipt_no' => $receipt_no,
                    'amount' => $amount
                ]
            ];

            $n = 1;
            for ($i = 0; $i < $n; $i++) {

                $modelsDonation[$i] = new Donations(['scenario' => 'withoutrecovery']);

            }
            Model::loadMultiple($modelsDonation, $DonationsArray);
            foreach ($modelsDonation as $modelDonation) {
                $modelDonation->source = 'cih';
                if (!$modelDonation->save()) {
                    $response['meta'] = [
                        'error' => true,
                        'message' => 'Failed transaction!',
                        'status_code' => 200
                    ];
                    $response['data'] = [
                        'cih_id' => $cih_id,
                        'is_sync' => '0',
                        'message' => 'Donation recording failed against mdp data!',
                        'error' => $modelDonation->getErrors()
                    ];

                } else {
                    $response['meta'] = [
                        'error' => true,
                        'message' => 'Successful transaction!',
                        'status_code' => 200
                    ];
                    $response['data'] = [
                        'cih_id' => $cih_id,
                        'is_sync' => '1'
                    ];

                }
            }
            return JsonHelper::asJson($response);
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Required Inputs not found!',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }
    }

    public function actionPostMdpBulk()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $requestInput = json_decode(urldecode($this->request['data']));

        $errorResult = [];
        $successResult = [];

        if(isset($requestInput) && !empty($requestInput)){

            foreach ($requestInput as $key => $input) {

                $DonationsArray['Donations'] = [
                    [
                        'sanction_no' => $input->sanction_no,
                        'branch_name' => $input->branchName,
                        'branch_code' => $input->branchCode,
                        'receive_date' => $input->receive_date,
                        'receipt_no' => $input->receipt_no,
                        'amount' => $input->amount
                    ]
                ];


                $n = 1;
                for ($i = 0; $i < $n; $i++) {

                    $modelsDonation[$i] = new Donations(['scenario' => 'withoutrecovery']);

                }
                Model::loadMultiple($modelsDonation, $DonationsArray);
                foreach ($modelsDonation as $modelDonation) {
                    $modelDonation->source = 'cih';
                    if (!$modelDonation->save()) {

                        $errorResult[$key]['cih_id'] = $input->cih_id;
                        $errorResult[$key]['teller_id'] = $input->teller_id;
                        $errorResult[$key]['receive_date'] = $input->receive_date;
                        $errorResult[$key]['is_sync'] = '0';
                        $errorResult[$key]['message'] = $modelDonation->getErrors();

                    } else {

                        $successResult[$key]['cih_id'] = $input->cih_id;
                        $successResult[$key]['is_sync'] = '1';
                        $successResult[$key]['message'] = 'Successfully recorded';

                    }
                }


            }
        }

        $response['meta'] = [
            'error' => false,
            'status_code' => 200
        ];
        $response['data'] = [
            'success' => $successResult,
            'error' => $errorResult
        ];

        return JsonHelper::asJson($response);
    }

    public function actionGetNetwork()
    {
        $key_value = $this->request['key'];
        if ($key_value == 'branches') {
            $response['data'] = Branches::find()->where(['status' => 1])->all();
        } elseif ($key_value == 'areas') {
            $response['data'] = Areas::find()->where(['status' => 1])->all();
        } elseif ($key_value == 'regions') {
            $response['data'] = Regions::find()->where(['status' => 1])->all();
        } elseif ($key_value == 'projects') {
            $response['data'] = Projects::find()->where(['status' => 1])->all();
        }

        if (!empty($response) && $response == null) {
            $response['meta'] = [
                'error' => true,
                'message' => 'No Data Found',
                'status_code' => 200
            ];
            return JsonHelper::asJson($response);
        } else {
            $response['meta'] = [
                'error' => false,
                'message' => 'Data Found',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }
    }

    public function actionGetMembersInfo()
    {
        $res=['success'=>null, 'message'=>null, 'data'=>null];

       try {

            $requestSanctionNos=$this->request['sanction_nos'];
            $sanctionNos=explode(',', $requestSanctionNos);
            $sanctionNosStr = implode(",", $sanctionNos);


            if($sanctionNos==null OR count($sanctionNos)<1){
                $res=['success'=>false, 'message'=>'sanction no is missing', 'data'=>null];
                return json_encode($res);
            }

            //$month_year=preg_replace('/-|:/', null, $month_year);

            $dump=[
                'sanction_nos'=>$sanctionNos,
            ];
            
            //$qry='select EXTRACT(YEAR_MONTH From FROM_UNIXTIME(loans.date_disbursed)) as disburse_date, loans.id as loanid, members.id as memberid, members.full_name, members.parentage, members.dob, members.gender, members_phone.phone, members.marital_status, members.cnic, member_info.cnic_issue_date, member_info.cnic_expiry_date, home_add.address as home_address, business_add.address as business_address, cities.id as city_id, cities.name as city_name, provinces.id as province_id, provinces.name as province_name, appraisals_social.total_family_members as dependent, appraisals_social.source_of_income as source_of_earning, appraisals_social.total_household_income as income_from, appraisals_social.total_household_income as income_to, loans.branch_id, branches.code as branch_code, loans.application_id,sanction_no,disbursed_amount,inst_amnt, (select sum(recoveries.amount) from recoveries where recoveries.loan_id=loans.id) as recovery, (select loans.disbursed_amount - sum(recoveries.amount) from recoveries where recoveries.loan_id=loans.id) as outstanding_amount from loans Left Join applications on applications.id=loans.application_id Left Join members on members.id=applications.member_id Inner Join members_phone on members_phone.member_id=members.id and members_phone.is_current=1 and phone_type="mobile" Left Join branches on branches.id=loans.branch_id Left Join member_info on member_info.member_id=members.id Left Join appraisals_social on appraisals_social.application_id=loans.application_id Left Join members_address home_add on home_add.member_id=members.id and home_add.address_type="home" Left Join members_address business_add on business_add.member_id=members.id and business_add.address_type="business" Left Join cities on cities.id=branches.city_id Left Join provinces on provinces.id=branches.province_id where loans.sanction_no IN('.implode(',', $sanction_nos).') group by loans.id';

            $qry="
               select 
                case loans.status
                when 'collected' then 1
                when 'loan completed' then 0
                    end as is_active,
                EXTRACT(YEAR_MONTH From FROM_UNIXTIME(loans.date_disbursed)) as disburse_date, 
                loans.id as loanid, 
                members.id as memberid, 
                members.full_name, 
                members.parentage, 
                members.dob, 
                members.gender, 
                members_phone.phone, 
                members.marital_status, 
                members.cnic, 
                member_info.cnic_issue_date, 
                member_info.cnic_expiry_date, 
                home_add.address as home_address, 
                business_add.address as business_address, 
                cities.id as city_id, 
                cities.name as city_name, 
                provinces.id as province_id, 
                provinces.name as province_name, 
                appraisals_social.total_family_members as dependent, 
                appraisals_social.source_of_income as source_of_earning, 
                appraisals_social.total_household_income as income_from, 
                appraisals_social.total_household_income as income_to, 
                loans.branch_id, branches.code as branch_code, 
                loans.application_id,
                sanction_no,
                disbursed_amount,
                inst_amnt,
                (select sum(recoveries.amount) from recoveries where recoveries.loan_id=loans.id) as recovery,
                (select loans.disbursed_amount - sum(recoveries.amount) 
                from recoveries where recoveries.loan_id=loans.id) as outstanding_amount
                from loans Left Join applications on applications.id=loans.application_id 
                Left Join members on members.id=applications.member_id 
                Inner Join members_phone on members_phone.member_id=members.id and members_phone.is_current=1 and phone_type='mobile' 
                Left Join branches on branches.id=loans.branch_id 
                Left Join member_info on member_info.member_id=members.id 
                Left Join appraisals_social on appraisals_social.application_id=loans.application_id 
                Left Join members_address home_add on home_add.member_id=members.id and home_add.address_type='home' 
                Left Join members_address business_add on business_add.member_id=members.id and business_add.address_type='business' 
                Left Join cities on cities.id=branches.city_id 
                Left Join provinces on provinces.id=branches.province_id 
                where loans.sanction_no IN($sanctionNosStr) and loans.status in('collected','loan completed')
                group by loans.id
            ";

            $queryData = Yii::$app->db->createCommand($qry);
            $members_info = $queryData->queryAll();

            $res=['success'=>true, 'dump'=>$dump, 'message'=>'Members Details Successfully fetched', 'data'=>$members_info];

               return json_encode($res);
           } catch (Exception $e) {
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           } catch (\yii\base\Exception $e){
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           }
    }

    public function actionGetExpireCnic()
    {
        $res=['success'=>null, 'message'=>null, 'data'=>null];

       try {

            if (!isset($this->request['branch_id'])) {
                $res=['success'=>false, 'message'=>'Branch is missing, please provide a branch', 'data'=>null]; 
                return json_encode($res); 
            }

            $branch_id=$this->request['branch_id'];

            $page_size=isset($this->request['page_size']) ? $this->request['page_size'] : 50;
            $page_no=isset($this->request['page_no']) ? $this->request['page_no'] : 0;
            $date=date("y-m-d",strtotime("+1 month"));

            $qry_total="SELECT COUNT(*) as total
                FROM member_info
                JOIN members ON member_info.member_id=members.id
                JOIN applications ON applications.member_id=members.id
                JOIN loans ON loans.application_id=applications.id
                WHERE  DATE(member_info.cnic_expiry_date) <= DATE('".$date."') AND loans.status='collected' AND loans.branch_id={$branch_id}";

            $queryTotalData = Yii::$app->db->createCommand($qry_total);
            $total = $queryTotalData->queryOne();




            $qry="SELECT member_info.id,members.full_name,members.parentage,members.cnic,member_info.cnic_issue_date,member_info.cnic_expiry_date
                FROM member_info
                JOIN members ON member_info.member_id=members.id
                JOIN applications ON applications.member_id=members.id
                JOIN loans ON loans.application_id=applications.id
                WHERE  DATE(member_info.cnic_expiry_date) <= DATE('".$date."') AND loans.status='collected' AND loans.branch_id={$branch_id} LIMIT {$page_size} OFFSET {$page_no}";

            $queryData = Yii::$app->db->createCommand($qry);
            $members_info = $queryData->queryAll();

            $data=[
                'members'=>$members_info,
                'total'=>$total
            ];


            $res=['success'=>true, 'message'=>'Members Details Successfully fetched', 'data'=>$data];

               return json_encode($res);
           } catch (Exception $e) {
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           } catch (\yii\base\Exception $e){
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           }
    }

    public function actionUpdateExpireCnic()
    {
        $res=['success'=>null, 'message'=>null, 'data'=>null];

       try {
        
            if (!isset($this->request['id']) OR !isset($this->request['cnic_issue_date']) OR !isset($this->request['cnic_expiry_date']) OR !isset($this->request['cnic_front']) OR !isset($this->request['cnic_back'])) {
                $res=['success'=>false, 'message'=>'Some parameters are missing, please provide missing parameters', 'data'=>null]; 
                return json_encode($res); 
            }

            $id=$this->request['id'];
            $cnic_issue_date=$this->request['cnic_issue_date'];
            $cnic_expiry_date=$this->request['cnic_expiry_date'];


            $model =MemberInfo::find()->where(['id'=>$id])->one();
            $model->cnic_issue_date = $cnic_issue_date;
            $model->cnic_expiry_date = $cnic_expiry_date;
            $model->save();


            $cnic_front = $this->request['cnic_front'];
            $cnic_back = $this->request['cnic_back'];

            $image_data = [];
            $image_data['image_data'] = $cnic_front;
            $image_data['parent_id'] = $this->request['id'];
            $image_data['parent_type'] = 'members';
            $image_data['image_type'] = 'cnic_front';
            $image = ImageHelper::syncImageObject($image_data);

            $image_data = [];
            $image_data['image_data'] = $cnic_back;
            $image_data['parent_id'] = $this->request['id'];
            $image_data['parent_type'] = 'members';
            $image_data['image_type'] = 'cnic_back';
            $image = ImageHelper::syncImageObject($image_data);



            $res=['success'=>true, 'message'=>'Members Details Successfully updated', 'data'=>null];

               return json_encode($res);
           } catch (Exception $e) {
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           } catch (\yii\base\Exception $e){
                $res=['success'=>false, 'message'=>'Something went wrong with this error'.$e->getMessage(), 'data'=>null];
               return json_encode($res);
           }
    }

}