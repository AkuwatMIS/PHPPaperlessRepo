<?php

namespace common\models\search;


use common\components\Helpers\ReportsHelper\RbacHelper;
use common\models\Loans;
use common\models\Regions;
use common\models\reports\Duelist;
use common\models\reports\Summary;
use common\models\Users;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class SummarySearch extends Summary
{
    /**
     * @inheritdoc
     */
    public $disbursement;
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'status', 'created_by'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['type', 'deposited', 'cih', 'name', 'short_name', 'code', 'uc', 'address','mobile', 'description','created_on', 'updated_on', 'opening_date','branch_manager','disbursement','due','overdue','recovery','date','schedule','province_id','division_id','district_id','city_id'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$export=false)
    {

     //   ((select sum(schdl_amnt) from schedules where schedules.loan_id=ANY(select loans.id from loans where loans.branch_id=branches.id ))-
  //  (select sum(credit) from recoveries where recoveries.loan_id=ANY(select loans.id from loans where loans.branch_id=branches.id )))as overdue,
// (@overdue+@recovery)as due ,
      //                $overdue:=(case when (@schedule-@recovery) <=0 then 0  when (@schedule-@recovery)>0 then (@schedule-@overdue) end) as overdue ,

        if (isset($params['SummarySearch']['date'])){
        $date = explode(' - ', $params['SummarySearch']['date']);}
        else{
          $date[0]=date('Y-m-d');
          $date[1]=date('Y-m-01',strtotime($date[0]));;
        }
        $query = Summary::find()->select('
                branches.id,branches.code,branches.name,branches.area_id,branches.region_id , branches.province_id as province_id, branches.division_id as division_id,branches.district_id as district_id,branches.city_id as city_id,
                @recovery :=(select sum(recoveries.credit) from recoveries where recoveries.branch_id = branches.id and (recoveries.recv_date between "'.($date[0]).'" and "'.($date[1]).'")) as recovery,

                @schedule :=((select sum(schdl_amnt) from schedules where schedules.branch_id=branches.id and (schedules.due_date between "'.($date[0]).'" and "'.($date[1]).'")))  as schedule,
                @overdue:=(case when (@schedule-@recovery) <=0 then 0  when (@schedule-@recovery)>0 then (@schedule-@recovery) end) as overdue ,
                (case when @overdue <=0 then @recovery  when @overdue>0 then @recovery+@overdue end) as due ,
                
                (select sum(loans.amountapproved) from loans where loans.branch_id = branches.id and (loans.datedisburse between "'.($date[0]).'" and "'.($date[1]).'")) as disbursement,
                (select sum(recoveries.credit) from recoveries where recoveries.branch_id = branches.id and (recoveries.recv_date between "'.($date[0]).'" and "'.($date[1]).'")) as recovery 
               
                    
        ')/*->where(['id'=>345])*/;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
       $dataProvider->pagination->pageSize = 50;
       // $query->joinWith('loan');
        //$query->joinWith('recoveries');

        $dataProvider->setSort([
            'attributes' => [

                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'area_id' => [
                    'asc' => ['area_id' => SORT_ASC],
                    'desc' => ['area_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'region_id' => [
                    'asc' => ['region_id' => SORT_ASC],
                    'desc' => ['region_id' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'disbursement' => [
                    'asc' => ['disbursement' => SORT_ASC],
                    'desc' => ['disbursement' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'recovery' => [
                    'asc' => ['recovery' => SORT_ASC],
                    'desc' => ['recovery' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due' => [
                    'asc' => ['due' => SORT_ASC],
                    'desc' => ['due' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'overdue' => [
                    'asc' => ['overdue' => SORT_ASC],
                    'desc' => ['overdue' => SORT_DESC],
                    'default' => SORT_DESC
                ]


            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
           'branches.province_id'=>$this->province_id,
           'branches.division_id'=>$this->division_id,
           'branches.district_id'=>$this->district_id,
           'branches.city_id'=>$this->city_id

            // 'disbursement' => $this->disbursement,
            //
            //'recovery' => $this->recovery,

        ]);
        $query->andFilterWhere(['in', 'id', $this->id]);
        $query->andFilterWhere(['like', 'branches.name', $this->name])
            ->andFilterWhere(['like', 'branches.code', $this->code])
            ->andFilterWhere(['like', 'branches.area_id', $this->area_id])
            ->andFilterWhere(['like', 'branches.region_id', $this->region_id]);
            //->andFilterWhere(['=', 'branches.disbursement', $this->disbursement])
            //->andFilterWhere(['=', 'branches.recovery', $this->recovery]);



        /* echo'<pre>';
         print_r($dataProvider->getModels());
         die();*/
        if($export){
            //  print_r($query->asArray()->all());
            // die();
            return $query;
        }else{
            return $dataProvider;
        }
       // return $dataProvider;
    }
    public function searchdisbursement($params)
    {
        // and (loans.dsb_status="collected")
        //(select count(*) from borrowers where branch_id=branches.id)as no_of_borrowers'
        $query=Summary::find()->select('branches.id,branches.id as branch_id,branches.name,branches.area_id,branches.region_id,
        (select name from areas where areas.id=branches.area_id)as area_name,
        (select name from regions where regions.id=branches.region_id)as region_name,
       @disbursement :=(select sum(loans.amountapproved) from loans where loans.branch_id = branches.id) ,
        (case when @disbursement IS NULL then 0  when @disbursement IS NOT NULL then @disbursement end) as disbursement ,
         @no_of_borrowers :=(select count(loans.id) from loans where (loans.branch_id = branches.id and loans.dsb_status="Collected")) as no_of_borrowers')->orderBy('region_id');
        $this->load($params);
        return $query->asArray()->all();
    }
    public function searchrecovery($params)
    {
        $query=Summary::find()->select('branches.id,branches.id as branch_id,branches.name,branches.area_id,branches.region_id,
        (select name from areas where areas.id=branches.area_id)as area_name,
        (select name from regions where regions.id=branches.region_id)as region_name,
        
         @mdp :=(select sum(recoveries.mdp) from recoveries where recoveries.branch_id = branches.id) as don,
         @recovery :=(select sum(recoveries.credit) from recoveries where recoveries.branch_id = branches.id) as recv,
         @no_of_borrowers :=(select count(distinct recoveries.loan_id) from recoveries where recoveries.branch_id = branches.id) as no_of_borrowers,

        (case when @recovery IS NULL then 0  when @recovery IS NOT NULL then @recovery end) as recovery ,
        (case when @mdp IS NULL then 0  when @mdp IS NOT NULL then @mdp end) as mdp ')->orderBy('region_id');
        $this->load($params);
        return $query->asArray()->all();
    }

    public function searchcih($params,$type,$table)
    {
        $query=Summary::find()->select(['branches.id, branches.id AS branch_id, branches.name, branches.area_id, branches.region_id, 
        (select name from areas where areas.id=branches.area_id)as area_name, 
        (select name from regions where regions.id=branches.region_id)as region_name,
         @total_amount := ( select coalesce(sum(r.amount), 0) as credit from '. $table.' r
          where r.branch_id = branches.id and r.source = "cc" ) as total_amount,
     
        @cih_amount := ( select coalesce(sum(r.amount), 0) as credit from '. $table.' r
           where r.id not in (select cih_type_id from cih_transactions_mapping where cih_transactions_mapping.cih_type_id = r.id and cih_transactions_mapping.type = "'. $type.'")
            and r.branch_id = branches.id and r.source = "cc" and r.transaction_id= 0) as cih_amount,
        @cih_partial := (select coalesce(sum(r.amount), 0) as credit from '. $table.' r 
            where r.id in (select cih_type_id from cih_transactions_mapping where cih_transactions_mapping.cih_type_id = r.id and cih_transactions_mapping.type = "'. $type.'")
            and r.branch_id = branches.id and r.source = "cc" and r.transaction_id = 0 ) as cih_partial,
         @cih_partial_sum := ( select coalesce(sum(cih_transactions_mapping.amount), 0) as credit from '. $table.' r 
            inner join cih_transactions_mapping on cih_transactions_mapping.cih_type_id = r.id
            where r.branch_id = branches.id and r.source = "cc" and r.transaction_id= 0 and cih_transactions_mapping.type = "'. $type.'" ) as cih_partial_sum ,
        @cih := (@cih_amount + (@cih_partial - @cih_partial_sum)) as cih,
        @deposited := @cih_partial_sum as deposited
        '
        ])
        ->orderBy('region_id');
        RbacHelper::searchRegionWiseFiltersOnBranch($query, '/frontend_recvsummaryrecoveries/');

        $this->load($params);

        /*print_r($query->asArray()->all());
        die();*/
        return $query->asArray()->all();
    }


    public function searchoverdue($params)
    {
        //@overdue:=(case when (@schedule-@recovery) <= 0 then 0  when (@schedule-@recovery)>0 then (@schedule-@recovery) end) as overdue
        $query=Summary::find()->select('branches.id,branches.id as branch_id,branches.name,branches.area_id,branches.region_id,
        (select name from areas where areas.id=branches.area_id)as area_name,
        (select name from regions where regions.id=branches.region_id)as region_name,
         @recovery :=(select sum(recoveries.credit) from recoveries where recoveries.branch_id = branches.id ) as recovery,
         @no_of_borrowers :=(select count(loans.id) from loans where (loans.branch_id = branches.id and loans.overdue>0)) as no_of_borrowers,

         @schedule :=((select sum(schdl_amnt) from schedules where schedules.branch_id=branches.id ))  as schedule, 
         @overdue:=(case when (@schedule-@recovery) <=0 then 0  when (@schedule-@recovery)>0 then (@schedule-@recovery) end) as overdue')->orderBy('region_id');
        $this->load($params);
        return $query->asArray()->all();
    }
   }
