<?php

namespace common\models;

use common\components\Helpers\AccountsReportHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "arc_account_reports".
 *
 * @property string $id
 * @property string $report_name
 * @property string $code
 * @property int $report_date
 * @property int $project_id
 * @property string $period
 * @property string $comments
 * @property int $status
 * @property int $is_verified
 * @property int $is_awp
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $do_update
 * @property int $do_delete
 * @property int $deleted
 */
class ArcAccountReports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arc_account_reports';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }

    public $_report_date;
    public $_created_at;
    public $_updated_at;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_name', 'code', 'project_id'], 'required'],
            [[ 'project_id', 'status', 'is_verified', 'is_awp' ,'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'do_update', 'do_delete', 'deleted'], 'integer'],
            [['period', 'comments'], 'string'],
            [['report_name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 20],
            [['report_date'],'safe']
           // [['report_date'], 'validateReportToIneger'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_name' => 'Report Name',
            'code' => 'Code',
            'report_date' => 'Report Date',
            'project_id' => 'Project ID',
            'period' => 'Period',
            'comments' => 'Comments',
            'status' => 'Status',
            'is_verified' => 'Is Verified',
            'is_awp' => 'Is Awp',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'do_update' => 'Do Update',
            'do_delete' => 'Do Delete',
            'deleted' => 'Deleted',
        ];
    }

    public function getArcAccountReportDetails()
    {
        return $this->hasMany(ArcAccountReportDetails::className(), ['arc_account_report_id' => 'id']);
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

    public function parseReport($region_id = 0, $area_id = 0, $branch_id=0){
        $big_array = array();
        $report_date_start = $this->report_date;
        $report_date_end = strtotime(date('Y-m-d 23:59:59', $this->report_date));
        if($this->period == 'monthly')
        {
            $report_date_start = strtotime(date('Y-m-01',$this->report_date));
            $report_date_end = strtotime(date('Y-m-t 23:59:59',$this->report_date));
        }

        if($this->code == 'recv')
        {

            if (date('Y-m') == date('Y-m', $this->report_date)) {
                if($this->is_awp == 0){
                    //0 for progress and 1 for accounts
//                    if (date('d') > 3) {
                        $report_date_start = strtotime(date('Y-m-01',$this->report_date));
//                        $report_date_end = strtotime(date('Y-m-d 23:59:59', strtotime('-3 day', time())));
                        $report_date_end = strtotime(date('Y-m-d 23:59:59', strtotime(time())));
//                    }
                }
            }
            $obj_data = AccountsReportHelper::get_recovery_data($report_date_start,$report_date_end, $region_id, $area_id, $branch_id, $this->project_id);
            foreach ($obj_data as &$one) {
                if (isset($big_array[$one['id']])) {
                    $big_array[$one['id']] = array_merge($big_array[$one['id']], AccountsReportHelper::parse_object($one));

                } else {
                    $big_array[$one['id']] = $one;
                }
            }

            foreach($big_array as &$one){

                $one['objects_count'] = round($one['objects_count']);
                $one['amount'] = $one['amount'];
            }

        }
        else if($this->code == 'disb')
        {
            $obj_data = AccountsReportHelper::get_disb_data($report_date_start,$report_date_end, $region_id, $area_id, $branch_id, $this->project_id);
            foreach ($obj_data as &$one) {
                if (isset($big_array[$one['id']])) {
                    $big_array[$one['id']] = array_merge($big_array[$one['id']], AccountsReportHelper::parse_object($one));

                } else {
                    $big_array[$one['id']] = $one;
                }
            }

            foreach($big_array as &$one){
                $one['objects_count'] = round($one['objects_count']);
                $one['no_of_tranches'] = round($one['no_of_tranches']);
                $one['amount'] = $one['amount'];
            }
        }
        else if($this->code == 'app_disb')
        {
            $obj_data = AccountsReportHelper::get_applications_count($report_date_start,$report_date_end, $region_id, $area_id, $branch_id, $this->project_id);
            foreach ($obj_data as &$one) {

                if (isset($big_array[$one['id']])) {
                    $big_array[$one['id']] = array_merge($big_array[$one['id']], AccountsReportHelper::parse_object($one));

                } else {
                    $big_array[$one['id']] = $one;
                }
            }

            foreach($big_array as &$one){
                $one['objects_count'] = round($one['objects_count']);
                $one['rejected_applications'] = isset($one['rejected_applications']) ? round($one['rejected_applications']) : 0;
                $one['disbursed_applications'] = isset($one['disbursed_applications']) ? round($one['disbursed_applications']) : 0;
            }
        }
        else if($this->code == 'don')
        {
            $obj_data = AccountsReportHelper::get_donation_data($report_date_start,$report_date_end,$region_id, $area_id, $branch_id, $this->project_id);
            foreach ($obj_data as &$one) {
                if (isset($big_array[$one['id']])) {
                    $big_array[$one['id']] = array_merge($big_array[$one['id']], AccountsReportHelper::parse_object($one));

                } else {
                    $big_array[$one['id']] = $one;
                }
            }

            foreach($big_array as &$one){
                $one['objects_count'] = round($one['objects_count']);
                $one['amount'] = $one['amount'];
            }
        }
        else
            if($this->code=='takaf'){
            $obj_data = AccountsReportHelper::get_takaful_data($report_date_start,$report_date_end,$region_id, $area_id, $branch_id, $this->project_id);
            foreach ($obj_data as &$one) {
                if (isset($big_array[$one['id']])) {
                    $big_array[$one['id']] = array_merge($big_array[$one['id']], AccountsReportHelper::parse_object($one));

                } else {
                    $big_array[$one['id']] = $one;
                }
            }
        }
        return $big_array;
    }

    public function add_account_report($report_name,$code,$type, $project_id,$progress_date){
        $connection = Yii::$app->db;
        $connection->createCommand("INSERT INTO arc_account_reports (id, report_name, code, report_date, project_id, created_at,updated_at, period, comments,status,assigned_to,created_by,updated_by,is_verified,do_update,do_delete,deleted)
                                VALUES (NULL,'".$report_name."','".$code."',   '".$progress_date."', '".$project_id."', '".$progress_date."', '".$progress_date."' ,'".$type."', 'Created By Cron',0 ,0,0,0,0,0,0,0)")->execute();
    }

    public function delete_last_month_report(){

        $connection = Yii::$app->db;
        $first_day_of_last_month = strtotime('first day of last month');
        $last_day_of_last_month =  strtotime('last day of last month');
        $connection->createCommand("update arc_account_reports set do_delete = 1 where report_date between '$first_day_of_last_month' and '$last_day_of_last_month' ")->execute();
    }

    /**
     *
     */
    public function delete_report(){
        $connection = Yii::$app->db;
       // $this->loadModel('Recovery');
        $connection->createCommand("update arc_account_reports set deleted = 1 where do_delete = 1 and deleted = 0 and do_update = 0 and is_verified = 0")->execute();
        $connection->createCommand("update arc_account_reports set do_delete = 0 where deleted = 1")->execute();
    }
}
