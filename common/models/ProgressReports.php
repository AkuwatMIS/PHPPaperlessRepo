<?php

namespace common\models;

use common\components\Helpers\ProgressReportHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "progress_reports".
 *
 * @property int $id
 * @property string $report_date
 * @property int $project_id
 * @property string $gender
 * @property string $period
 * @property string $comments
 * @property int $status
 * @property int $is_verified
 * @property int $do_update
 * @property int $do_delete
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ProgressReportDetails[] $progressReportDetails
 * @property Projects $project
 */
class ProgressReports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'progress_reports';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    public $_report_date;
    public $_created_at;
    public $_updated_at;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_date', 'project_id', 'gender', 'period', 'assigned_to', 'created_by'], 'required'],
            [['project_id', 'do_update', 'do_delete', 'deleted', 'assigned_to', 'created_by', 'updated_by'/*,'report_date'*/], 'integer'],
            [['comments'], 'string'],
            [['_report_date','_created_at','_updated_at'], 'safe'],
            [['report_date'],'validateReportToIneger'],
            [['gender'], 'string', 'max' => 6],
            [['period'], 'string', 'max' => 20],
            [['status', 'is_verified'], 'string', 'max' => 3],
            //[['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_date' => 'Report Date',
            'project_id' => 'Project ID',
            'gender' => 'Gender',
            'period' => 'Period',
            'comments' => 'Comments',
            'status' => 'Status',
            'is_verified' => 'Is Verified',
            'do_update' => 'Do Update',
            'do_delete' => 'Do Delete',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgressReportDetails()
    {
        return $this->hasMany(ProgressReportDetails::className(), ['progress_report_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
    public function validateReportToIneger($attribute){
        /*print_r($this->report_date);
        die('aa');*/
        if ($this->isNewRecord) {
        $this->report_date = strtotime($this->report_date);
        }
    }
    public function beforeCreate(){
        $this->status = '0';
        $this->do_update = 0;
        $this->do_delete = 0;
        $this->deleted = 0;
        $this->is_verified = '0';
        $this->created_by=1;
        $this->assigned_to =1;
        $this->created_at = strtotime(date("Y-m-d H:i:s"));
        $this->updated_at = strtotime(date("Y-m-d H:i:s"));
    }

    public function parseProgress_($region_id = 0, $area_id = 0, $branch_id=0){
        $big_array = array();
        $this->report_date = strtotime(date('Y-m-d 23:59:59', $this->report_date));
        $male_female = ProgressReportHelper::get_male_female($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);
        $activeloans_cumdis = ProgressReportHelper::get_activeloans_cumdisbursement($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);
        $due_overdue = ProgressReportHelper::get_due_overdue($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);

        //Compile all data in one big array
        foreach ($male_female as &$one) {

            if (isset($big_array[$one['id']])) {
                $big_array[$one['id']] = array_merge($big_array[$one['id']], ProgressReportHelper::parse_male_female($one['male_female_others']));

            } else {
                $big_array[$one['id']] = ProgressReportHelper::parse_male_female($one['male_female_others']);
            }
        }

        foreach($activeloans_cumdis as &$one)
        {
            if(isset($big_array[$one['id']])){
                $big_array[$one['id']] = array_merge($big_array[$one['id']],ProgressReportHelper::parse_activeloans_cumdis_new($one) );
            }else{
                $big_array[$one['id']] = ProgressReportHelper::parse_activeloans_cumdis_new($one);
            }
        }
        foreach($due_overdue as &$one)
        {
            if(isset($big_array[$one['id']])){
                $big_array[$one['id']] = array_merge($big_array[$one['id']],ProgressReportHelper::parse_due_overdue($one['borrower_overdue']) );
            }else{
                $big_array[$one['id']] = ProgressReportHelper::parse_due_overdue($one['borrower_overdue']);
            }
        }
        foreach($big_array as &$one){
            $one['gender'] = $this->gender;
            $one['family_loans'] = round($one['male']);
            $one['female_loans'] = round($one['female']);
            $one['active_loans'] = round($one['active_loans']);
            $one['no_of_loans'] = round($one['total']);
            $one['cum_disb'] = $one['cum_disbursement'];
            $one['cum_recv'] = !empty($one['cum_recovery']) ? $one['cum_recovery'] : 0;
            $one['overdue_borrowers'] = $one['no_borrowers'];
            $one['overdue_amount'] = $one['overdue'];
            $one['par_amount'] = $one['par'];
            $one['not_yet_due'] = $one['cum_disbursement'] - $one['cum_due'];
            $one['olp_amount'] = $one['cum_disbursement'] - $one['cum_recovery'];
            $one['recovery_percentage'] = 0.00;
            $one['par_percentage'] = $one['olp_amount'];
            $one['overdue_percentage'] = 0.00;
            if($one['cum_due'] != 0){
                $one['overdue_percentage'] = round((($one['overdue'] * 100) / $one['cum_due']),2 );
            }
            if($one['cum_due'] != 0){
                $one['recovery_percentage'] = round((($one['cum_recovery'] * 100) / $one['cum_due']), 2);
            }
            else{
                $one['recovery_percentage'] = 100.00;
            }
            if($one['olp_amount'] != 0){
                $one['par_percentage'] = round((($one['par'] * 100) / $one['olp_amount']), 2);
            }

            $one['cih'] = 0;
            $one['mdp'] = !empty($one['mdp']) ? $one['mdp'] : 0;
        }
        return $big_array;
    }

    public function parseProgress($region_id = 0, $area_id = 0, $branch_id=0){
        $big_array = array();
        $this->report_date = strtotime(date('Y-m-d 23:59:59', $this->report_date));
       // $male_female = ProgressReportHelper::get_male_female($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);
        $activeloans_cumdis = ProgressReportHelper::get_activeloans_cumdisbursement($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);
        //$due_overdue = ProgressReportHelper::get_due_overdue($this->report_date, $region_id, $area_id, $branch_id, $this->project_id, $this->gender);

        //Compile all data in one big array
        /*foreach ($male_female as &$one) {

            if (isset($big_array[$one['id']])) {
                $big_array[$one['id']] = array_merge($big_array[$one['id']], ProgressReportHelper::parse_male_female($one['male_female_others']));

            } else {
                $big_array[$one['id']] = ProgressReportHelper::parse_male_female($one['male_female_others']);
            }
        }*/

        foreach($activeloans_cumdis as &$one)
        {
            if(isset($big_array[$one['id']])){
                $big_array[$one['id']] = array_merge($big_array[$one['id']],ProgressReportHelper::parse_activeloans_cumdis_new($one) );
            }else{
                $big_array[$one['id']] = ProgressReportHelper::parse_activeloans_cumdis_new($one);
            }
        }
        /*foreach($due_overdue as &$one)
        {
            if(isset($big_array[$one['id']])){
                $big_array[$one['id']] = array_merge($big_array[$one['id']],ProgressReportHelper::parse_due_overdue($one['borrower_overdue']) );
            }else{
                $big_array[$one['id']] = ProgressReportHelper::parse_due_overdue($one['borrower_overdue']);
            }
        }*/
        foreach($big_array as &$one){
            $one['gender'] = $this->gender;
            $one['family_loans'] = round($one['male']);
            $one['female_loans'] = round($one['female']);
            $one['active_loans'] = round($one['active_loans']);
            $one['no_of_loans'] = round($one['total']);
            $one['members_count'] = round($one['members_count']);
            $one['cum_disb'] = $one['cum_disbursement'];
            $one['cum_recv'] = !empty($one['cum_recovery']) ? $one['cum_recovery'] : 0;
            $one['overdue_borrowers'] = $one['no_borrowers'];
            $one['overdue_amount'] = $one['overdue'];
            $one['par_amount'] = $one['par'];
            $one['not_yet_due'] = $one['cum_disbursement'] - $one['cum_due'];
            $one['olp_amount'] = $one['cum_disbursement'] - $one['cum_recovery'];
            $one['recovery_percentage'] = 0.00;
            $one['par_percentage'] = $one['olp_amount'];
            $one['overdue_percentage'] = 0.00;
            if($one['cum_due'] != 0){
                $one['overdue_percentage'] = round((($one['overdue'] * 100) / $one['cum_due']),2 );
            }
            if($one['cum_due'] != 0){
                $one['recovery_percentage'] = round((($one['cum_recovery'] * 100) / $one['cum_due']), 2);
            }
            else{
                $one['recovery_percentage'] = 100.00;
            }
            if($one['olp_amount'] != 0){
                $one['par_percentage'] = round((($one['par'] * 100) / $one['olp_amount']), 2);
            }

            $one['cih'] = 0;
            $one['mdp'] = !empty($one['mdp']) ? $one['mdp'] : 0;
        }
        return $big_array;
    }


    /**
     *
     */
    public function add_progress_report($type, $project_id,$male_female,$progress_date){

        $connection = Yii::$app->db;
        $connection->createCommand("INSERT INTO progress_reports (id, report_date, project_id, created_at,updated_at, period, comments,gender,status,assigned_to,created_by,is_verified,do_update,do_delete,deleted)
                                VALUES (NULL, '".$progress_date."', '".$project_id."', '".$progress_date."', '".$progress_date."' ,'".$type."', 'Created By Cron',0 ,0,0,0,0,0,0,0)")->execute();
    }

    /**
     *
     */
    public function delete_last_month_progress(){

        $connection = Yii::$app->db;
        $first_day_of_last_month = strtotime('first day of last month');
        $last_day_of_last_month =  strtotime('last day of last month');
        $connection->createCommand("update progress_reports set do_delete = 1 where report_date between '$first_day_of_last_month' and '$last_day_of_last_month' ")->execute();
    }

    /**
     *
     */
    public function delete_progress(){
        $connection = Yii::$app->db;
        $this->loadModel('Recovery');
        $connection->createCommand("update progress_reports set deleted = 1 where do_delete = 1 and deleted = 0 and do_update = 0 and is_verified = 0")->execute();
        $connection->createCommand("update progress_reports set do_delete = 0 where deleted = 1")->execute();
    }

}
