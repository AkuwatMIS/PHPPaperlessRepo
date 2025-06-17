<?php
namespace console\controllers;

use common\components\Helpers\AccountsReportHelper;
use common\models\ArcAccountReportDetails;
use common\models\ArcAccountReports;
use common\models\Branches;
use common\models\ProgressReports;
use common\models\ProgressReportUpdate;
use common\models\Projects;
use Mpdf\Tag\Progress;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\console\Controller;
use common\models\ProgressReportDetails;
use common\components\Helpers\ProgressReportHelper;
class AccountReportsController extends Controller
{

    public function actionGenerateReport($id, $region_id=0, $area_id=0, $branch_id=0){
        $branch_ids=[];
        $account_report = $this->findModel($id);
        $big_array = $account_report->parseReport($region_id, $area_id, $branch_id);
        //get branches list
        $branches = Branches::find()->where(['deleted' => 0])->asArray()->all();
        $branches_list = AccountsReportHelper::parse_branches($branches);

        foreach($big_array as $branch_id =>$ba){

            $account_report_detail_exists = ArcAccountReportDetails::find()
                ->where([
                    'arc_account_report_id'=>$account_report->id,
                    'branch_id'=>$branch_id,
                ])->one();

            if($account_report_detail_exists === null){
                $account_report_detail = new ArcAccountReportDetails();
                $account_report_detail->attributes = $ba;
                $account_report_detail->arc_account_report_id = $id;
                $account_report_detail->set_branch_data($branch_id, $branches_list);
                if($account_report_detail->save(false)){
                    $branch_ids[]=$account_report_detail->branch_id;
                }else{
                    echo 'Not Generated';
                }
            }else{
                $account_report_detail_exists->attributes = $ba;
                $account_report_detail_exists->set_branch_data($branch_id, $branches_list);
                if($account_report_detail_exists->save(false)){
                    $branch_ids[]=$account_report_detail_exists->branch_id;
                }else{
                    echo 'Not Updated';
                }
            }
        }

        //entry with zero record`
        $account_report_update=ArcAccountReports::find()->where(['id'=>$id])->one();
        $account_report_update->is_awp=0;
        $account_report_update->save(false);


        $with_zero_record = ArcAccountReportDetails::find()
            ->where(['arc_account_report_id' => $account_report->id])->andWhere(['not in', 'branch_id', $branch_ids])->all();
        foreach ($with_zero_record as $d) {
            $d->objects_count = 0;
            $d->amount = 0;
            $d->disbursed_applications = 0;
            $d->rejected_applications = 0;
            $d->save(false);
        }
        //$result = $progress_report->create_progress_report($big_array);
        //echo $result;
    }

    /**
     *
     */

    public function actionAddReportDailyProject(){
        $type = 'daily-project';
        $report_date = strtotime(date("Y-m-d"));
        $projects = Projects::find()->select('id')->where(['status'=>1])->all();
        $reports = ['recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary','takaf'=>'Takaful Summary'];
        foreach ($reports as $k => $report) {
            foreach ($projects as $project) {
                $account_report = new ArcAccountReports();
                $project = $project->id;
                $account_report->add_account_report($report, $k, $type, $project, $report_date);
            }
        }
    }

    public function actionAddReport($type, $project){
        $account_report = new ArcAccountReports();
        $report_date = strtotime(date("Y-m-d"));
        $reports = ['recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary','takaf'=>'Takaful Summary',];
        foreach ($reports as $k => $report) {
            $account_report->add_account_report($report,$k,$type, $project, $report_date);
        }
    }

    /**
     *
     */
//   php yii account-reports/add-daily-report
    public function actionAddDailyReport(){
        $report_date = strtotime(date('Y-m-t'));
        $type = 'monthly';
        $reports = ['recv'=>'Recovery Summary','disb'=>'Disbursement Summary','don'=>'Donation Summary','takaf'=>'Takaful Summary'];
        foreach ($reports as $k => $report) {
            $account_reports = ArcAccountReports::find()->where(['report_date' => $report_date,'code' => $k,'period' => $type])->all();
            if(empty($account_reports))
            {
                $account_report = new ArcAccountReports();
                $projects = Projects::find()->select('id')->where(['status'=>1])->all();
                foreach ($projects as $project) {
                    $account_report = new ArcAccountReports();
                    $project = $project->id;
                    $account_report->add_account_report($report, $k,$type, $project, $report_date);
                }
                $account_report->add_account_report($report,$k,$type, 0, $report_date);
            }
            foreach ($account_reports as $account_report)
            {
                $account_report->do_update = 1;
                $account_report->is_awp = 1;
                $account_report->save();
            }
        }
    }
    public function actionAddReportBulk($code,$from,$to,$project){
        if($code=='don'){
            $name='Donation Summary';
        }elseif ($code=='recv'){
            $name='Recovery Summary';
        }else if($code=='app_disb'){
            $name='Application Disbursement Report';
        }else if($code=='disb'){
            $name='Disbursement Summary';
        }
        else if($code=='takaf'){
            $name='Takaful Summary';
        }
        $period = 'monthly';
        $dates=[];
        $d=$from;
        for ($i=0;$d<$to;$i++){
            $dates[]=strtotime(date('Y-m-t-23:59',strtotime("+$i months",strtotime($from))));
            $d=date('Y-m-t-23:59',strtotime("+$i months",strtotime($from)));
        }
        foreach ($dates as $dt) {
            print_r(strtotime(date('Y-m-t',$dt)).',,,,'.$dt.',,');
            $account_report = ArcAccountReports::find()->where(['between','report_date',strtotime(date('Y-m-t',$dt)),$dt])->andWhere(['period' => $period,'project_id'=>$project,'code'=>$code])->one();
            if(empty($account_report))
            {
                $account_report = new ArcAccountReports();
                $account_report->add_account_report($name, $code,$period, $project, $dt);
            }else {
                $account_report->do_update = 1;
                $account_report->save();
            }
        }
    }
    /**
     *
     */
    public function actionAddProjectsMonthlyReport(){
        $projects = Projects::find()->select('id')->where(['status'=>1])->all();
        $reports = ['recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary','takaf'=>'Takaful Summary'];
        foreach ($reports as $k => $report) {
            foreach ($projects as $project) {

                $account_report = new ArcAccountReports();
                $type = 'monthly';
                $project = $project->id;
                $account_date = strtotime('last day of last month');
                $account_report->add_account_report($report, $k,$type, $project, $account_date);
            }
        }
    }

    /**
     *
     */
    public function actionUpdateProjectsDailyReport(){

        $account_reports_updated = ArcAccountReports::find()->where(['status'=>'1','period'=>'daily-project','is_verified'=>'0','do_delete'=>'0','deleted'=>'0'])->all();
        foreach($account_reports_updated as $account_report){
            $account_report->do_update = 1;
            $account_report->report_date = strtotime(date("Y-m-d"));
            $account_report->save();
        }
    }

    public function actionDeleteProjectsDailyReport(){
        $account_reports_updated = ArcAccountReports::find()->where(['status'=>'1','period'=>'daily-project','is_verified'=>'0','do_delete'=>'0','deleted'=>'0'])->all();
        foreach($account_reports_updated as $account_report){
            $account_report->do_delete = 1;
            $account_report->save();
        }
    }

    /**
     *
     */
    public function actionDoDelete(){
        //$progress_report = new ProgressReports();
        //$progress_report->delete_last_month_progress();
    }

    /**
     *
     */
    public function actionDelete(){
        $account_report = new ArcAccountReports();
        $account_report->delete_report();
    }

    /**
     *
     */

//   php /var/www/paperless_web/yii account-reports/execute-account-report
    public function actionExecuteAccountReport(){

        //Generate All InActive Reports
        $account_reports_active = ArcAccountReports::find()->where(['status'=>'0','is_verified'=>'0','do_update'=>'0','do_delete'=>'0','deleted'=>'0'])->all();
        foreach($account_reports_active as $account_report){
            $this->actionGenerateReport($account_report->id);
            $account_report->status = 1;
            $account_report->updated_at = strtotime(date("Y-m-d H:i:s"));
            $account_report->save(false);
        }

        //Update All reports where do_update=1
        $account_reports_updated = ArcAccountReports::find()->where(['status'=>'1','do_update'=>'1','is_verified'=>'0','do_delete'=>'0','deleted'=>'0'])->all();
        foreach($account_reports_updated as $account_report){
            $this->actionGenerateReport($account_report->id);
            $account_report->do_update = 0;
            $account_report->updated_at = strtotime(date("Y-m-d H:i:s"));
            $account_report->save(false);
        }
    }

//  15 12 01 * * php /var/www/paperless_web/yii account-reports/update-report-bulk

    public function actionUpdateReportBulk()
    {
        $start=strtotime(date('Y-m-d',strtotime('last day of last month')));
        $end=strtotime(date('Y-m-d 23:59:59',strtotime('last day of last month')));
        $account_reports = ArcAccountReports::find()->where(['between', 'report_date', $start, $end])->andWhere(['status'=>1])->all();
        foreach ($account_reports as $account_report) {
            $account_report->do_update = 1;
            $account_report->save();
        }
        $progress_reports = ProgressReports::find()->where(['between', 'report_date', $start, $end])->andWhere(['status'=>1])->all();
        foreach ($progress_reports as $progress_report) {
            $progress_report->do_update = 1;
            $progress_report->save();
        }
    }



    /**
     * Finds the Loans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ProgressReports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArcAccountReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}