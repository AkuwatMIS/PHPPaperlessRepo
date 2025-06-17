<?php
namespace console\controllers;

use common\models\Branches;
use common\models\BranchProjectsMapping;
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
class ProgressReportController extends Controller
{
    /*public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }*/

    /**
     *
     */


    public function actionGenerateReport($id, $region_id=0, $area_id=0, $branch_id=0){

        $progress_report = $this->findModel($id);

//        if($progress_report->project_id !=0){
//            $project_branch_mapping= BranchProjectsMapping::find()->where(['project_id'=>$progress_report->project_id])->asArray()->all();
//            foreach ($project_branch_mapping as $branch_data) {
//                $branch_ids[] = $branch_data['branch_id'];
//            }
//            $branches= Branches::find()->where(['in','id' , $branch_ids])->andWhere(['deleted' => 0])->asArray()->all();
//            $branches_list = ProgressReportHelper::parse_branches($branches);
//        }else{
//            $branches = Branches::find()->where(['deleted' => 0])->asArray()->all();
//            $branches_list = ProgressReportHelper::parse_branches($branches);
//        }


        $big_array = $progress_report->parseProgress($region_id, $area_id, $branch_id);
        //get branches list
        $branches = Branches::find()->where(['deleted' => 0])->asArray()->all();
        $branches_list = ProgressReportHelper::parse_branches($branches);

        foreach($big_array as $branch_id =>$ba){

            $progress_report_detail_exists = ProgressReportDetails::find()
                                        ->where([
                                            'progress_report_id'=>$progress_report->id,
                                            'branch_id'=>$branch_id,
                                            'gender'=>$progress_report->gender,
                                        ])->one();

            if($progress_report_detail_exists === null){
                /*print_r($progress_report_detail_exists);
                die();*/
                $progress_report_detail = new ProgressReportDetails();
                $progress_report_detail->attributes = $ba;
                $progress_report_detail->progress_report_id = $id;
                $progress_report_detail->set_branch_data($branch_id, $branches_list);
                if($progress_report_detail->save(false)){
                    //echo 'Generated';
                }else{
                    echo 'Not Generated';
                }
            }else{
                $progress_report_detail_exists->attributes = $ba;
                $progress_report_detail_exists->set_branch_data($branch_id, $branches_list);
                if($progress_report_detail_exists->save(false)){
                    //echo 'Updated';
                }else{
                    echo 'Not Updated';
                }
            }
        }
        //$result = $progress_report->create_progress_report($big_array);
        //echo $result;
    }

    /**
     *
     */
    public function actionAddProgressReport($type, $project,$male_female){
        $progress_report = new ProgressReports();
        $progress_date = strtotime(date("Y-m-d H:i:s"));
        $progress_report->add_progress_report($type, $project,$male_female,$progress_date);
    }

    /**
     *
     */
    public function actionAddDailyProgress(){
        $progress_report = new ProgressReports();
        $type = 'daily';
        $project = '0';
        $male_female = '0';
        //$progress_date = strtotime(date("Y-m-d"));
        $progress_date = strtotime("-1 days", strtotime(date("Y-m-d")));
        $progress_report->add_progress_report($type, $project,$male_female,$progress_date);

        $progress_report = new ProgressReports();
        $type = 'daily';
        $project = '3';
        $male_female = '0';
        //$progress_date = strtotime(date("Y-m-d"));
        $progress_date = strtotime("-1 days", strtotime(date("Y-m-d")));
        $progress_report->add_progress_report($type, $project,$male_female,$progress_date);

        $progress_report = new ProgressReports();
        $type = 'daily';
        $project = '59';
        $male_female = '0';
        //$progress_date = strtotime(date("Y-m-d"));
        $progress_date = strtotime("-1 days", strtotime(date("Y-m-d")));
        $progress_report->add_progress_report($type, $project,$male_female,$progress_date);
    }

    /**
     *
     */
    public function actionAddProjectsMonthlyProgress(){
        $projects = Projects::find()->select('id')->where(['status'=>1])->all();

        foreach ($projects as $project){
            //print_r($project->id);
            $progress_report = new ProgressReports();
            $type = 'monthly';
            $project = $project->id;
            $male_female = '0';
            $progress_date =  strtotime('last day of last month');
            $progress_report->add_progress_report($type, $project,$male_female,$progress_date);
            //die();
        }
    }

    /**
     *
     */
    public function actionUpdateProjectsDailyProgress(){

        $progress_reports_updated = ProgressReports::find()->where(['status'=>'1','period'=>'daily-project','is_verified'=>'0','do_delete'=>'0','deleted'=>'0'])->all();
        foreach($progress_reports_updated as $progress_report){
            $progress_report->do_update = 1;
            $progress_report->report_date = strtotime(date("Y-m-d"));
            $progress_report->save();
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
        $progress_report = new ProgressReports();
        $progress_report->delete_progress();
    }

    /**
     *
     */
    public function actionExecuteProgressReport(){

        //Generate All InActive Reports
        $progress_reports_active = ProgressReports::find()->where(['status'=>'0'/*,'is_verified'=>'0'*/,'do_update'=>'0','do_delete'=>'0','deleted'=>'0'])->all();

        foreach($progress_reports_active as $progress_report){
            $this->actionGenerateReport($progress_report->id);
            $progress_report->status = 1;
            $progress_report->updated_at = strtotime(date("Y-m-d H:i:s"));
            $progress_report->save(false);
        }

        //Update All reports where do_update=1
        $progress_reports_updated = ProgressReports::find()->where(['status'=>'1','do_update'=>'1',/*'is_verified'=>'0',*/'do_delete'=>'0','deleted'=>'0'])->all();
        foreach($progress_reports_updated as $progress_report){
            $this->actionGenerateReport($progress_report->id);
            $progress_report->do_update = 0;
            $progress_report->updated_at = strtotime(date("Y-m-d H:i:s"));
            $progress_report->save(false);
        }
        //Update specific region,area,branch
        /*$progress_reports_update_structure = ProgressReportUpdate::find()->where(['status' => '0'])->all();
        foreach($progress_reports_update_structure as $update_reports){
            $region_id=$update_reports->region_id;
            $area_id=$update_reports->area_id;
            $branch_id=$update_reports->branch_id;
            $this->actionGenerateReport($update_reports->report_id,$region_id,$area_id,$branch_id);
            $update_reports->status = 1;
            $update_reports->updated_at = strtotime(date("Y-m-d H:i:s"));
            $update_reports->save();
        }*/
    }


    public function actionExecuteProgressReportSpecific(){


        //Update specific region,area,branch
        $progress_reports_update_structure = ProgressReportUpdate::find()->where(['status' => '0'])->all();
        foreach($progress_reports_update_structure as $update_reports){
            $region_id=$update_reports->region_id;
            $area_id=$update_reports->area_id;
            $branch_id=$update_reports->branch_id;
            $this->actionGenerateReport($update_reports->report_id,$region_id,$area_id,$branch_id);
            $update_reports->status = 1;
            $update_reports->updated_at = strtotime(date("Y-m-d H:i:s"));
            $update_reports->save();
        }
    }

    public function actionUpdateExecuteProgressReport(){
        //Update specific region,area,branch
        $progress_reports_update_structure = ProgressReportUpdate::find()->where(['status' => '0'])->all();
        foreach($progress_reports_update_structure as $update_reports){
            $region_id=$update_reports->region_id;
            $area_id=$update_reports->area_id;
            $branch_id=$update_reports->branch_id;
            $this->actionGenerateReport($update_reports->report_id,$region_id,$area_id,$branch_id);
            $update_reports->status = 1;
            $update_reports->updated_at = strtotime(date("Y-m-d H:i:s"));
            $update_reports->save();
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
        if (($model = ProgressReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}