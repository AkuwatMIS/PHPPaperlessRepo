<?php
namespace common\models\search;
use common\models\Operations;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TakafulPaidSearch extends Operations{
    public $cnic;
    public $full_name;
    public $parentage;
    public $region;
    public $area;
    public $branch;
    public $sanction_no;
    public $project;
    public $receipt_no;
    public $receive_date;
    public $credit;
    public $loan_amount;
    public function rules()
    {
        return [
            [['receive_date','region_id','area_id'],'required'],
            [['id', 'area_id', 'branch_id', 'region_id', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['status'], 'safe'],
            [['full_name','parentage', 'cnic', 'region', 'area', 'branch', 'project','receive_date','cibstatus','Nadra','loan_amount','PMT','Account_Verification'], 'safe'],
        ];
    }
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function search($params, $export = false){
        $end = $start = '';
        $cur_date=strtotime(date("Y-m-d H:i:s", strtotime('+5 hours')));
        $six_month_back_date=strtotime(date("Y-m-d",strtotime("-5 Months")));
       // else{
          //  $end = strtotime(date('Y-m-d-23:59', (strtotime('last day of this month'))));
          //  $start = strtotime(date('Y-m-11', strtotime('last day of last month')));
      //  }
        $query= Operations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('application');
        $query->joinWith('application.member');
        $this->load($params);
        if (!$this->validate()) {

            return $dataProvider;
        }
        $query->andFilterWhere([
            'operations.region_id'=>$this->region_id,
            'operations.area_id'=>$this->area_id,
            'operations.branch_id'=>$this->branch_id,
            'operations.project_id'=>$this->project_id,
            'members.cnic'=>$this->cnic,
            'members.full_name'=>$this->full_name,
            'members.parentage'=>$this->parentage
        ]);
        $query->andFilterWhere(['operations.operation_type_id'=>2]);
        if (!is_null($this->receive_date) && strpos($this->receive_date, ' - ') !== false) {
            $date = explode(' - ', $this->receive_date);
            $query->andFilterWhere(['between', 'receive_date', strtotime($date[0]), strtotime($date[1])]);
        } else if(isset($params['TakafulPaidSearch']['receive_date']))
        {
            $query->andFilterWhere(['between','operations.receive_date',$params['TakafulPaidSearch']['receive_date'],$cur_date]);

        }else {
            $this->receive_date = date("Y-m-d", strtotime("-5 Months")) . ' - ' . date("Y-m-d");
            $query->andFilterWhere(['between', 'operations.receive_date', $six_month_back_date, $cur_date]);
            //$query->andFilterWhere(['between','applications.application_date',$six_month_back_date,$cur_date]);
            //$query->andFilterWhere(['like','application_date' , strtotime($this->date_disbursed)]);
        }





        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }


    }
}