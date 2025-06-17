<?php

namespace frontend\modules\api\controllers;

use common\components\Helpers\AcagHelper;
use common\components\Helpers\ImageHelper;
use common\models\Applications;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Visits;
use Yii;


class OpenApiController extends RestController
{
    private $validApiKey = 'sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas'; // Define your valid API key here

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return $behaviors;
    }

    public function actionFetchMemberDetail($cnic)
    {
        $request = Yii::$app->request;
        $apiKey = $request->headers->get('x-api-key'); // Get API key from header

        // Validate API Key
        if ($apiKey !== $this->validApiKey) {
            return $this->sendFailedResponse(401, 'Invalid API Key');
        }

        // Fetch member details
        $model = Loans::find()
            ->join('INNER JOIN', 'applications', 'applications.id = loans.application_id')
            ->join('INNER JOIN', 'members', 'members.id = applications.member_id')
            ->join('INNER JOIN', 'members_phone', 'members_phone.member_id = members.id')
            ->where([
                'applications.deleted' => 0,
                'loans.status' => 'collected',
                'members.cnic' => $cnic
            ])
            ->select([
                'members.full_name as Name',
                'members.parentage as Parentage',
                'members_phone.phone as MobilePhone',
                'loans.sanction_no as SanctionNumber'
            ])
            ->asArray()
            ->one();

        if ($model) {
            return $this->sendSuccessResponse(200, $model);
        } else {
            return $this->sendFailedResponse(404, 'Member details not found');
        }
    }

    public function actionAddVisit()
    {
        $visit = $this->request;
        $errors = [];
        $success = [];
        $success_response = [];
        $data = [];
        $nic = $visit['parent_id'] ?? null; // Use null coalescing operator

        if (!$nic) {
            $errors[] = ['error' => 'Parent ID (CNIC) is missing.'];
            return $this->sendSuccessResponse(200, $errors);
        }

        $application = Loans::find()
            ->innerJoin('applications', 'applications.id = loans.application_id')
            ->innerJoin('members', 'members.id = applications.member_id')
            ->where(['loans.status' => 'collected'])
            ->andWhere(['members.cnic' => $nic])
            ->andWhere(['loans.project_id' => 132])
            ->select(['applications.id'])
            ->one();

        if ($application && $application->id > 0) {
            $visit['parent_id'] = $application->id;
            $flag = true;
            $model = new Visits();
            $model->attributes = $visit;
            $model->estimated_start_date = isset($visit['estimated_start_date']) ? strtotime($visit['estimated_start_date']) : 0;
            $model->created_by = 6174;

            if (!$model->save()) {
                $flag = false;
                $errors[] = ['temp_id' => $visit['temp_id'] ?? null, 'error' => 'Visit save failed: ' . json_encode($model->getErrors())]; // Include validation errors
            } else {
                $base64_decode_data = base64_decode($visit['image']);
                $images_data = json_decode(gzdecode($base64_decode_data));
                foreach ($images_data as $img_data) {
                    $image_data = [];
                    $image_data['image_data'] = $img_data;
                    $image_data['parent_id'] = $model->id;
                    $image_data['parent_type'] = 'visits';
                    $image_data['image_type'] = 'visit_' . rand(1, 9);
                    $image = ImageHelper::syncImageObject($image_data);
                    if (!$image) {
                        $flag = false;
                    }
                }
            }

            if ($flag) {
                $success[] = ['id' => $model->id, 'temp_id' => $visit['temp_id'] ?? null];
                $success_response[] = $visit['temp_id'] ?? null;
            }

            if (!in_array($visit['temp_id'] ?? null, $success_response)) {
                $errors[] = ['temp_id' => $visit['temp_id'] ?? null, 'error' => 'Visit not saved (image or save fail)'];
            }
        } else {
            $errors[] = ['temp_id' => $visit['temp_id'] ?? null, 'error' => 'Application not found'];
        }

        if (empty($success) && !empty($errors)) {
            $data['response_status'] = 'error';
        } elseif (!empty($success) && empty($errors)) {
            $data['response_status'] = 'success';
        } else {
            $data['response_status'] = 'warning';
        }

        if($data['response_status'] == 'success'){
            $application = Applications::find()->where(['id' => $model->parent_id])->one();
            if ($application && $application->project_id == 132) {
                $loan = Loans::find()->where(['application_id'=>$application->id])->one();
                if(!empty($loan) && $loan!=null){

                   $result =  AcagHelper::actionPush($application,'VisitPercent','VisitPercent',0,date('Y-m-d'),null,$loan);
                    if($result){
                        $data['success'] = $success;
                        return $this->sendSuccessResponse(200, $data);
                    }else{
                        $data['errors'] = $errors;
                        return $this->sendSuccessResponse(201, $data);
                    }
                }else{
                    $data['success'] = $success;
                    return $this->sendSuccessResponse(200, $data);
                }
            }
        } else {
            $data['errors'] = $errors;
            return $this->sendSuccessResponse(201, $data);
        }
    }

    public function actionScheduleData(){
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $branchID = Yii::$app->getRequest()->getQueryParam('branch_id');
        $paramsDate = Yii::$app->getRequest()->getQueryParam('report_date');

        $due_date = strtotime(date('Y-m-10-00:00', (strtotime($paramsDate))));
        $disburse_date = strtotime(date("Y-m-d-23:59", $due_date));
        $receipt_date = strtotime(date('Y-m-t-23:59', (strtotime($paramsDate . '- 1 months'))));

        if ($branchID != 0) {

            $query = "
                select
                    rg.id AS region_id,
                    ar.id AS area_id,
                    br.id AS branch_id,
                    m.full_name,
                    m.parentage,
                    m.cnic,
                    maddr.address,
                    mphon.phone,
                    ln.sanction_no,
                    sch.id AS installment_id,
                    ln.project_id,
                    pr.name AS project_name,
                    pr.bank_prefix,
                    DATE_FORMAT(FROM_UNIXTIME(sch.due_date), '%Y-%m-%d') AS due_date,
                    DATE_FORMAT(FROM_UNIXTIME(ln.date_disbursed), '%Y-%m-%d') AS date_disbursed,
                    ln.disbursed_amount,
                    
                    @recovery:=(select COALESCE(sum(amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) 'recovery',
                    
                   @balance:=(ln.disbursed_amount-@recovery) as 'olp',
                    
                   @due_amount:=((select COALESCE(sum(schdl_amnt),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@recovery) 'actual_due_amount',
                    
                    
                   @receive_charges_amount:=(select COALESCE(sum(charges_amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_charges_amount',
                   @due_charges_amount:=((select COALESCE(sum(charges_schdl_amount),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@receive_charges_amount) 'actual_charges_amount_due',

                   @receive_sale_tax:=(select COALESCE(sum(credit_tax),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_sale_tax',
                   @due_amount_tax:=((select COALESCE(sum(charges_schdl_amnt_tax),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@receive_sale_tax) 'actual_sale_tax_due'
                   ,@due_1 := CASE
                       WHEN @due_amount > sch.schdl_amnt THEN @due_amount
                       WHEN sch.schdl_amnt IS NULL THEN @due_amount
                       WHEN @balance < sch.schdl_amnt THEN @balance
                       ELSE sch.schdl_amnt
                    END AS 'due_amount',
            
                    @due_2 := CASE
                       WHEN @due_charges_amount > sch.charges_schdl_amount THEN @due_charges_amount
                       WHEN sch.charges_schdl_amount IS NULL THEN @due_charges_amount
                       ELSE sch.charges_schdl_amount
                       END AS 'due_charges_amount',
                   @due_3 := CASE
                       WHEN @due_amount_tax > sch.charges_schdl_amnt_tax THEN @due_amount_tax
                       WHEN sch.charges_schdl_amnt_tax IS NULL THEN @due_amount_tax
                       ELSE sch.charges_schdl_amnt_tax
                       END AS 'due_amount_tax',
                   
                   @due_1+@due_2+@due_3 'total_due'
                   
                    from
                    loans ln
                    inner join applications app
                    on ln.application_id = app.id and app.deleted=0 and app.status='approved'
                    inner join members m
                    on app.member_id = m.id
                    left join members_phone mphon
                    on mphon.member_id = m.id and mphon.is_current=1 and LENGTH(mphon.phone) >= 11
                    left join members_address maddr
                    on maddr.member_id = m.id and maddr.is_current=1
                    Inner join projects pr
                    on pr.id=ln.project_id
                    inner join groups grp
                    on ln.group_id = grp.id
                    inner join regions rg
                    on ln.region_id = rg.id
                    inner join areas ar
                    on ln.area_id=ar.id
                    inner join branches br
                    on ln.branch_id = br.id
                    LEFT JOIN schedules sch ON sch.loan_id = ln.id and sch.due_date<=$due_date
                    where
                    ln.status in ('collected')
                    and
                    ln.date_disbursed <= $disburse_date
                    AND br.id in ($branchID)
                    group by ln.sanction_no
                    having olp > 0 and due_amount > 0 AND due_date IS NOT NULL
                ";


            try {
                $queryData = Yii::$app->db->createCommand($query);
                $branchActiveLoans = $queryData->queryAll();
            } catch (\Exception $e) {
                $response['meta'] = [
                    'error' => true,
                    'message' => $error = $e->getMessage(),
                    'status_code' => 201
                ];
                return $this->sendSuccessResponse(201, $response);
            }
            if (empty($branchActiveLoans) && $branchActiveLoans == null) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'No active loan exists!',
                    'status_code' => 201
                ];
                return $this->sendSuccessResponse(201, $response);
            } else {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Data fetched successfully!',
                ];
                $response['data'] = $branchActiveLoans;
                return $this->sendSuccessResponse(200, $response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!'
            ];
            $response['data'] = [];
            return $this->sendSuccessResponse(201, $response);
        }
    }

    public function actionBorrowerScheduleData(){
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $cnic = Yii::$app->getRequest()->getQueryParam('cnic');
        $paramsDate = Yii::$app->getRequest()->getQueryParam('report_date');

        $due_date = strtotime(date('Y-m-10-00:00', (strtotime($paramsDate))));
        $disburse_date = strtotime(date("Y-m-d-23:59", $due_date));
        $receipt_date = strtotime(date('Y-m-t-23:59', (strtotime($paramsDate . '- 1 months'))));

        if (isset($cnic) && !empty($cnic) && $cnic != '') {

            $query = "
                select
                    rg.id AS region_id,
                    ar.id AS area_id,
                    br.id AS branch_id,
                    m.full_name,
                    m.parentage,
                    m.cnic,
                    maddr.address,
                    mphon.phone,
                    ln.sanction_no,
                    sch.id AS installment_id,
                    ln.project_id,
                    pr.name AS project_name,
                    pr.bank_prefix,
                    DATE_FORMAT(FROM_UNIXTIME(sch.due_date), '%Y-%m-%d') AS due_date,
                    DATE_FORMAT(FROM_UNIXTIME(ln.date_disbursed), '%Y-%m-%d') AS date_disbursed,
                    ln.disbursed_amount,
                    
                    @recovery:=(select COALESCE(sum(amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) 'recovery',
                    
                   @balance:=(ln.disbursed_amount-@recovery) as 'olp',
                    
                   @due_amount:=((select COALESCE(sum(schdl_amnt),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@recovery) 'actual_due_amount',
                    
                    
                   @receive_charges_amount:=(select COALESCE(sum(charges_amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_charges_amount',
                   @due_charges_amount:=((select COALESCE(sum(charges_schdl_amount),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@receive_charges_amount) 'actual_charges_amount_due',

                   @receive_sale_tax:=(select COALESCE(sum(credit_tax),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_sale_tax',
                   @due_amount_tax:=((select COALESCE(sum(charges_schdl_amnt_tax),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@receive_sale_tax) 'actual_sale_tax_due'
                   ,@due_1 := CASE
                       WHEN @due_amount > sch.schdl_amnt THEN @due_amount
                       WHEN sch.schdl_amnt IS NULL THEN @due_amount
                       WHEN @balance < sch.schdl_amnt THEN @balance
                       ELSE sch.schdl_amnt
                    END AS 'due_amount',
            
                    @due_2 := CASE
                       WHEN @due_charges_amount > sch.charges_schdl_amount THEN @due_charges_amount
                       WHEN sch.charges_schdl_amount IS NULL THEN @due_charges_amount
                       ELSE sch.charges_schdl_amount
                       END AS 'due_charges_amount',
                   @due_3 := CASE
                       WHEN @due_amount_tax > sch.charges_schdl_amnt_tax THEN @due_amount_tax
                       WHEN sch.charges_schdl_amnt_tax IS NULL THEN @due_amount_tax
                       ELSE sch.charges_schdl_amnt_tax
                       END AS 'due_amount_tax',
                   
                   @due_1+@due_2+@due_3 'total_due'
                   
                    from
                    loans ln
                    inner join applications app
                    on ln.application_id = app.id and app.deleted=0 and app.status='approved'
                    inner join members m
                    on app.member_id = m.id
                    left join members_phone mphon
                    on mphon.member_id = m.id and mphon.is_current=1 and LENGTH(mphon.phone) >= 11
                    left join members_address maddr
                    on maddr.member_id = m.id and maddr.is_current=1
                    Inner join projects pr
                    on pr.id=ln.project_id
                    inner join groups grp
                    on ln.group_id = grp.id
                    inner join regions rg
                    on ln.region_id = rg.id
                    inner join areas ar
                    on ln.area_id=ar.id
                    inner join branches br
                    on ln.branch_id = br.id
                    LEFT JOIN schedules sch ON sch.loan_id = ln.id and sch.due_date=$due_date
                    where
                    ln.status in ('collected')
                    and ln.date_disbursed <= $disburse_date
                    AND m.cnic='$cnic' group by m.cnic
                ";


            try {
                $queryData = Yii::$app->db->createCommand($query);
                $branchActiveLoans = $queryData->queryAll();
            } catch (\Exception $e) {
                $response['meta'] = [
                    'error' => true,
                    'message' => $error = $e->getMessage(),
                    'status_code' => 201
                ];
                return $this->sendSuccessResponse(201, $response);
            }
            if (empty($branchActiveLoans) && $branchActiveLoans == null) {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'No active loan exists!',
                    'status_code' => 201
                ];
                return $this->sendSuccessResponse(201, $response);
            } else {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Data fetched successfully!',
                ];
                $response['data'] = $branchActiveLoans;
                return $this->sendSuccessResponse(200, $response);
            }
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Borrower Data not exists!'
            ];
            $response['data'] = [];
            return $this->sendSuccessResponse(201, $response);
        }
    }
}
