<?php


namespace console\controllers;


use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\CibDataCheckHelper;
use common\components\Helpers\CibHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\CibTypes;
use common\models\DisbursementDetails;
use common\models\FilesAccounts;
use common\models\FilesApplication;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\MemberInfo;
use common\models\Members;
use common\models\MembersAccount;
use common\widgets\Cib\Cib;
use Ratchet\App;
use SimpleXMLElement;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\web\Response;
use kartik\mpdf\Pdf;

class CibController extends Controller
{
//php yii cib/run-cib
    public function actionRunCib()
    {
        //CibDataCheckHelper::getReport();
        $applications = Applications::find()
            ->join('inner join', 'applications_cib', 'applications.id=applications_cib.application_id')
            ->join('inner join', 'branches', 'branches.id=applications.branch_id')
            ->where(['applications_cib.status' => 0])
            ->andWhere(['applications_cib.type' => 0])
            ->andWhere(['in', 'branches.province_id', [1,6]])
            ->andWhere(['in', 'applications_cib.cib_type_id', [0,1]])
            ->andWhere(['applications.deleted' => 0])
            ->andWhere(['in', 'applications.status', ['pending','approved']])
//            ->andWhere(['applications.id'=>6272265])
            ->limit(500)
            ->all();

        CibHelper::actionCib($applications, 'Tasdeeq');
    }


//    php yii cib/run-data-check
    public function actionRunDataCheck(){
        //CibDataCheckHelper::actionCibDataCheck();
        $applications=Applications::find()
            ->join('inner join','applications_cib','applications.id=applications_cib.application_id')
            ->join('inner join', 'branches', 'branches.id=applications.branch_id')
            ->andWhere(['applications_cib.status'=>'0'])
            ->andWhere(['in', 'branches.province_id', [2,3,4,7,8]])
            ->andWhere(['applications_cib.type'=>'0'])
            ->andWhere(['applications.deleted'=>'0'])
            ->andWhere(['in', 'applications.status', ['pending','approved']])
//            ->andFilterWhere(['=','applications.id','6566200'])
            ->all();

        foreach ($applications as $application) {

            $response = CibHelper::actionCibDataCheck($application,'DataCheck');
            //$dataStatus =  $response['s:Envelope']['s:Body']['getBureauCreditReportV3_1Response']['getBureauCreditReportV3_1Result']['a:CRNewResponse']['a:Status'];
            $dataStatus =  $response['s:Envelope']['s:Body']['getBureauCreditReportV4Response']['getBureauCreditReportV4Result']['a:CRNewResponse']['a:Status'];

            if($dataStatus == 'CR'){ echo 'CR'; print_r($response);
                //$dataResponse =  $response['s:Envelope']['s:Body']['getBureauCreditReportV3_1Response']['getBureauCreditReportV3_1Result']['a:CRNewResponse']['a:Report'];
                $dataResponse =  $response['s:Envelope']['s:Body']['getBureauCreditReportV4Response']['getBureauCreditReportV4Result']['a:CRNewResponse']['a:Report'];
                $xml = new SimpleXMLElement($dataResponse,LIBXML_NOCDATA);
                $cib_application = ApplicationsCib::find()->where(['application_id'=>$application->id])->one();

                $cib_application->cib_type_id = 2;
                $cib_application->status      = 1;
                $cib_application->type        = 0;
                $cib_application->response    = Json::encode($xml);
//                $cib_application->response    = $dataResponse;
                $cib_application->save();
            }else{
                echo 'else';print_r($response);
                //$dataResponse =  $response['s:Envelope']['s:Body']['getBureauCreditReportV3_1Response']['getBureauCreditReportV3_1Result']['a:CRNewResponse']['a:Report'];
                $dataResponse =  $response['s:Envelope']['s:Body']['getBureauCreditReportV4Response']['getBureauCreditReportV4Result']['a:CRNewResponse']['a:Report'];
                try {
                    $xml = new SimpleXMLElement($dataResponse);
                } catch (\Exception $e){
                    $dataResponse = '<root>'.$dataResponse.'</root>';
                    $xml = new SimpleXMLElement($dataResponse);
                }
                $cib_application = ApplicationsCib::find()->where(['application_id'=>$application->id])->one();
                $cib_application->cib_type_id = 2;
                $cib_application->status      = 3;
                $cib_application->type        = 0;
                $cib_application->response    = Json::encode($xml);
//                $cib_application->response    = $dataResponse;
                $cib_application->save();
            }
        }

    }

//    public function actionDataCheckPasswordReset(){
//        $action = 'reset';
//       $response = CibHelper::actionDataCheckPassword($action,'DataCheck');
//       print_r($response);
//       die();
//    }
//
//    public function actionDataCheckPasswordExpiry(){
//        $action = 'expiry';
//        $response = CibHelper::actionDataCheckPassword($action,'DataCheck');
//        print_r($response);
//        die();
//    }


    public function actionRunCibExtend()
    {
        //CibDataCheckHelper::getReport();
        $applications = Applications::find()
            ->join('inner join', 'applications_cib', 'applications.id=applications_cib.application_id')
            ->join('inner join', 'branches', 'branches.id=applications.branch_id')
            ->where(['in', 'applications.status', ['pending','approved']])
            ->andWhere(['in', 'applications.id', [6431245]])
            ->all();

        CibHelper::actionCib($applications, 'Tasdeeq');
    }
    public function actionTransferData()
    {
        $application_files = FilesApplication::find()->all();
        foreach ($application_files as $file) {
            $cib = ApplicationsCib::find()->where(['application_id' => $file->application_id])->one();
            if (empty($cib)) {
                $cib = new ApplicationsCib();
                $cib->application_id = $file->application_id;
                $cib->cib_type_id = 2;
                $cib->fee = 0;
                $cib->receipt_no = '';
                $cib->type = 1;
                $cib->status = 1;
                $cib->file_path = $file->file_path;
                $cib->created_by = 1;
                if (!$cib->save(false)) {
                    print_r($cib->getErrors());
                }
            }
        }
    }

    public function actionPushTasdeeqData()
    {
        $body = [];
        $models = ApplicationsCib::find()->where(['status' => 0, 'type' => 1])->all();
        foreach ($models as $model) {
            $body[] = [
                "application_id" => isset($model->application_id) ? $model->application_id : '',
                "cnic" => isset($model->application->member->cnic) ? str_replace("-", "", $model->application->member->cnic) : '',
                "full_name" => isset($model->application->member->full_name) ? $model->application->member->full_name : '',
                //"dateOfBirth" => isset($application->member->dob) ? date('d-M-Y', $application->member->dob) : '01-jan-1970',
                "city" => isset($model->application->branch->city->name) ? $model->application->branch->city->name : '',
                "requested_amount" => isset($model->application->req_amount) ? '' . round($model->application->req_amount) . '' : '0',
                "gender" => isset($model->application->member->gender) ? ($model->application->member->gender) : '',
                "address" => isset($mmodel->application->member->businessAddress->address) ? $model->application->member->businessAddress->address : '',
                "father_name" => isset($model->application->member->parentage) ? $model->application->member->parentage : ''
            ];
        }
        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://116.71.135.115/cib/tasdeeq/requests/push-requests.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
        print_r($body);
        die();
    }

    public function actionConvertToFile()
    {
        $total_records = ApplicationsCib::find()->where(['status' => 1, 'transfered' => 0])->count();
        while ($total_records > 0) {
            $cibs = ApplicationsCib::find()->where(['status' => 1, 'transfered' => 0])->limit(10)->all();
            foreach ($cibs as $cib) {
                print_r($cib->id);
                $file_base_path = ImageHelper::getAttachmentPath() . '/cib_data/' . date('Y-m') . '/';
                if (!file_exists($file_base_path)) {
                    mkdir($file_base_path, 0777, true);
                }
                $file_name = $cib->application->member->cnic . '_cib.pdf';
                $file_path = $file_base_path . $file_name;
                $response = json_decode($cib->response);
                if (!empty($response) && empty($cib->file_path)) {
                    $content = \common\widgets\Cib\Cib::widget(['model' => $response]);
                    // setup kartik\mpdf\Pdf component
                    $pdf = new Pdf([
                        'mode' => Pdf::MODE_CORE,
                        'format' => Pdf::FORMAT_LEGAL,
                        'filename' => $file_path,
                        'orientation' => Pdf::ORIENT_PORTRAIT,
                        'destination' => Pdf::DEST_FILE, //Pdf::DEST_FILE
                        'content' => $content,
                        'cssFile' => '/css/cib.css',
                        'options' => ['title' => 'cib #'],
                        'methods' => [
                            'SetHeader' => ['Akhuwat'],
                            'SetFooter' => ['{PAGENO}'],
                        ]
                    ]);

                    $cib->file_path = '/cib_data/' . date('Y-m') . '/' . $file_name;
                    $cib->transfered = 1;
                    if (!$cib->save()) {
                        print_r($cib->getErrors());
                    }
                    $pdf->render();
                } else {

                    $cib->transfered = 2;
                    $cib->save();
                }

            }
            $total_records = $total_records - 10;
        }
    }

    function clean($string)
    {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        return $string;
//        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function actionSaveCib()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/cib-data.csv';
        $myfile = fopen($file_path, "r");
        while (($fileop = fgetcsv($myfile)) !== false) {
            $applicationSearch = Applications::find()
                ->join('inner join', 'members', 'applications.member_id=members.id')
                ->where(['=', 'applications.id', trim($fileop[0])])
                ->andWhere(['=', 'members.cnic', trim($fileop[1])])
                ->asArray()
                ->one();
            if ($applicationSearch) {
                $checkCib = ApplicationsCib::find()
                    ->where(['=', 'applications_cib.application_id', trim($fileop[0])])
                    ->one();
                if (!$checkCib) {
                    $cibModel = new ApplicationsCib();
                    $cibModel->application_id = trim($fileop[0]);
                    $cibModel->cib_type_id = 1;
                    $cibModel->fee = 20;
                    $cibModel->receipt_no = trim($fileop[2]);
                    $cibModel->created_by = 1;
                    $cibModel->status = 0;
                    $cibModel->type = 0;
                    $cibModel->save(false);
                    echo 'cib-saved' . PHP_EOL;
                } else {
                    echo 'app-cib-exists' . PHP_EOL;
                }
            }
        }
    }

    public function actionCibResponseOld()
    {
        $cib_response = [1 => 'ZCIR20210406'];
        foreach ($cib_response as $report) {
            $batch_folder = [1 => 'batch1', 2 => 'batch2'];
            foreach ($batch_folder as $batch) {
                $file_path = ImageHelper::getAttachmentPath() . '/cib_data/' . '/' . $report . '/' . $batch . '/' . $batch . '.csv';
                $myfile = fopen($file_path, "r");
                $header = fgetcsv($myfile);
                $i = 2;
                while (($fileop = fgetcsv($myfile)) !== false) {
                    $member = Members::find()->where(['cnic' => $fileop[0]])->one();
                    if (!empty($member)) {
                        $application = Applications::find()->where(['member_id' => $member->id, 'deleted' => 0])->andWhere(['<>', 'project_id', 59])->orderBy(['id' => SORT_DESC])->one();
                        if (!empty($application)) {
                            $response_pdf = ImageHelper::getAttachmentPath() . '/cib_data/' . $report . '/' . $batch . '/' . $i . '_' . $fileop[0] . '.pdf';
                            if (file_exists($response_pdf)) {
                                $cib = ApplicationsCib::find()->where(['application_id' => $application->id])->one();
                                if ($cib) {
                                    if ($cib->status == 1) {
                                        echo $member->cnic;
                                        echo '<br>';
                                        echo $application->id;
                                        echo '<br>';
                                        echo $member->id;
                                        echo 'response exists.';
                                    } else {
                                        $cib->status = 1;
                                        $cib->type = 1;
                                        $cib->file_path = '../cib_data/ZCIR20210406/' . $i . '_' . $fileop[0] . '.pdf';
                                        if ($cib->save()) {
                                            echo $cib->id;
                                            echo '<br>';
                                            print_r($i . '_' . $fileop[0] . '.pdf');
                                            echo '<br>';
                                        } else {
                                            var_dump($cib->getErrors());
                                            die();
                                        }
                                    }
                                } else {
                                    echo $application->id;
                                    echo 'Application does not have cib entry.';
                                }

                            } else {
                                echo 'File does not exists!' . $batch;
                            }
                        } else {
                            echo 'application does not exists!' . $batch . ' cnic' . $fileop[0];
                        }

                    } else {
                        echo 'member does not exists!' . $batch . ' cnic' . $fileop[0];
                    }
                    $i++;
                }
            }
        }
    }

    public function actionCibResponse()
    {
        $lastDateOfLastMonth = date('d-m-Y 00:00:00', strtotime('last day of last month'));
        $updated_date = strtotime(date('Y-m-d'));
        $folder_month = date('Y-m');
        $cib_response = [1 => 'Z20221205'];
        foreach ($cib_response as $report) {
            $file_path = ImageHelper::getAttachmentPath() . '/cib_data/' . $report . '/applications_cib.csv';
            $myfile = fopen($file_path, "r");
            $header = fgetcsv($myfile);
            $i = 2;
            while (($fileop = fgetcsv($myfile)) !== false) {
                $response_pdf = ImageHelper::getAttachmentPath() . '/cib_data/' . $report . '/' . $fileop[0] . '.pdf';
                if (file_exists($response_pdf)) {
                    $cib = ApplicationsCib::find()->where(['id' => $fileop[0]])->one();
                    if ($cib) {
//                        if ($cib->status == 1) {
//                            echo $cib->id;
//                            echo 'response exists.';
//                        } else {
                            $cib->status = 1;
                            $cib->type = 1;
                            $cib->updated_at = $lastDateOfLastMonth;
                            $cib->file_path = '../cib_data/' . $report . '/' . $fileop[0] . '.pdf';
                            if ($cib->save()) {
                                echo $cib->id;
                                echo '<br>';
                                print_r($fileop[0] . '.pdf');
                                echo '<br>';
                            } else {
                                var_dump($cib->getErrors());
                                die();
                            }
//                        }
                    } else {
                        echo $cib->id;
                        echo 'File does not exists!';
                    }

                } else {
                    echo 'File does not exists!';
                }
                $i++;
            }
        }
    }
//php yii cib/manual-run-cib

    public function actionManualRunCib()
    {
        $app_ids = [6359211];
        foreach ($app_ids as $app_id) {
            $applications = Applications::find()
                ->join('inner join', 'applications_cib', 'applications.id=applications_cib.application_id')
                ->join('inner join', 'branches', 'branches.id=applications.branch_id')
                ->where(['applications.id'=>$app_id])
                ->andWhere(['applications_cib.status' => 0])
                ->all();

            CibHelper::actionCib($applications, 'Tasdeeq');
        }

    }

    public function actionCreateCib()
    {
        $sanctions = [];
        foreach ($sanctions as $sanction) {
            $loan = Loans::find()->where(['sanction_no'=>$sanction])->one();
            if(!empty($loan) && $loan!=null){
                $cib_model = new ApplicationsCib();
                $cib_model->application_id = $loan->application_id;
                $cib_model->cib_type_id = 1;
                $cib_model->fee = 14;
                $cib_model->receipt_no = strval($loan->application_id);
                $cib_model->status = 0;
                $cib_model->type = 1;
                $cib_model->transfered = 2;
                $cib_model->created_by = $loan->created_by;
                $cib_model->created_at = $loan->created_at;
                $cib_model->updated_at = $loan->created_at;
                if($cib_model->save()){
                    echo '---saved---';
                }else{
                    print_r($cib_model->getErrors());
                    echo '---error-at--'.$sanction;
                    die();
                }
            }
        }
    }
}