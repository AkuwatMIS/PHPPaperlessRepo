<?php
namespace common\models;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ConfigHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "takaful_due".
 *
 * @property int $id





 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 *  @property int $loan_id
 *    @property int $olp
 * @property int $takaful_amnt
 *  @property int $disb_date
 *  @property int overdue_date
 * @property int overdue_amnt
 * @property int takaf_rec_date

 * @property int $status
 * @property int $credit
 * @property int $takaful_year

 * @property string $created_at
 * @property string $updated_at
 *

 * @property Areas $area
 * @property Branches $branch
 * @property Members $member
 * @property Products $product
 * @property Projects $project
 * @property Regions $region


 * @property Loans[] $loan
 * @property Operations[] $operations

 * @property Projects[] $projects

 */


class Takafuldue extends \yii\db\ActiveRecord{
    public static function tableName()
    {
        return 'takaful_due';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }

    public function rules()
    {
        return [
            [[ 'region_id', 'area_id', 'branch_id','status' ], 'integer'],
        ];
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id'])->andOnCondition(['loans.deleted'=>'0']);
    }
    public  static function getTakafulData(){
        $lastMonthDate =  strtotime('last day of previous month');

        $sql = "SELECT
                  branches.id as branch_id,
	              regions.id as region_id,
    		      areas.id as area_id,
    		      loans.id as loan_id,
                  loans.date_disbursed AS 'disburse_date',
                  concat(right(DATE(FROM_UNIXTIME(date_disbursed)),5),-YEAR(CURDATE())) As 'due_date',
                  
                   @disburse_amount := (select disbursed_amount) as 'disbursed_amount',
                   @credit:=(select coalesce(sum(recoveries.amount),0)
                   from recoveries where recoveries.loan_id = loans.id and recoveries.deleted=0 AND recoveries.receive_date<=$lastMonthDate) as'credits',
                   @olp_amount:=(( @disburse_amount - @credit)) as 'olp',
                   @takaful_amount:=( (Round((@olp_amount*0.385)/100)) )As 'takaful_amount'
                FROM
                    loans
                    Inner JOIN regions on loans.region_id=regions.id
                    Inner Join areas on loans.area_id=areas.id
                    Inner Join branches on loans.branch_id=branches.id
                WHERE
                ((TIMESTAMPDIFF(MONTH, DATE(FROM_UNIXTIME(date_disbursed)), CURRENT_DATE()))%35)=0 and
                   loans.status='collected' and loans.project_id in (77,78,79) and loans.deleted=0";

        $query = Yii::$app->db->createCommand($sql);
        $data = $query->queryAll();


        return $data;
    }


    public function AddDueTakaf($a){

        $overDueDate = strtotime("+36 months", $a['disburse_date']);
        $existing = Takafuldue::find()->where(['loan_id'=>$a['loan_id']])->andWhere(['overdue_date'=>$overDueDate])->one();
        if(empty($existing) && $existing==null){
            $model = new Takafuldue();
            $model->branch_id = $a['branch_id'];
            $model->region_id = $a['region_id'];
            $model->area_id = $a['area_id'];
            $model->loan_id = $a['loan_id'];
            $model->disb_date = $a['disburse_date'];
            $model->olp = $a['olp'];
            $model->takaful_amnt = $a['takaful_amount'];
            $model->overdue_date = $overDueDate;
            $model->overdue_amnt = $a['takaful_amount'];
            $model->status = 0;
            $model->takaful_year=date("Y");
            if($model->save()){

            }else{
                var_dump($model->getErrors());
                die();
            }
        }

    }

}