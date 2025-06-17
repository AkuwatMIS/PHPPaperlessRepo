<?php

namespace common\models\search;

use common\models\Donations;
use common\models\Recoveries;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Branches;

/**
 * BranchesSearch represents the model behind the search form about `common\models\Branches`.
 */
class RecoveryBranchesSearch extends Branches
{
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['area.name', 'region.name', 'city.name']);
    }
    public $address;
    public $longitude;
    public $latitude;
    public $branch_manager;
    public $branches = array();
    public $type;
    public $table_name;
    public $date;
    public $receive_date;
    public $receipt_no;
    public $amount;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'city_id', 'tehsil_id', 'district_id', 'division_id', 'province_id', 'country_id', 'cr_division_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['type', 'name', 'code', 'uc', 'village', 'address', 'mobile', 'description', 'opening_date', 'status', 'created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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


    public function search_cih_collector($params,$export=false)
    {
        $this->load($params);


        if(isset($params['RecoveryBranchesSearch'])){
            $params=$params['RecoveryBranchesSearch'];
        }

        if($this->table_name=='donations') {
            $query = Donations::find()->select(["donations.id", "donations.application_id", "donations.loan_id", "donations.amount", "donations.region_id", "donations.area_id", "donations.branch_id", "donations.receipt_no", "donations.receive_date", "donations.created_by","donations.source"])
                ->leftJoin('cih_transactions_mapping', 'cih_transactions_mapping.cih_type_id=donations.id  AND cih_transactions_mapping.type="donat"')
                //->join('inner join','branches','branches.id = donations.branch_id')
                ->andWhere(['=', 'donations.source', "cc"])
                ->andWhere(['=', 'donations.transaction_id', 0])
                ->andFilterWhere(['=', 'donations.created_by', Yii::$app->user->getId()])
                ->andFilterWhere(['is', 'cih_transactions_mapping.cih_type_id', new \yii\db\Expression('null')]);
        }
        elseif ($this->table_name=='recoveries'){
            $query = Recoveries::find()->select(["recoveries.id", "recoveries.application_id", "recoveries.loan_id", "recoveries.amount", "recoveries.region_id", "recoveries.area_id", "recoveries.branch_id", "recoveries.receipt_no", "recoveries.receive_date", "recoveries.created_by","recoveries.source"])
                ->leftJoin('cih_transactions_mapping', 'cih_transactions_mapping.cih_type_id=recoveries.id  AND cih_transactions_mapping.type="recov"')
                //->join('inner join','branches','branches.id = recoveries.branch_id')
                ->andWhere(['=', 'recoveries.source', "cc"])
                ->andWhere(['=', 'recoveries.transaction_id', 0])
                ->andFilterWhere(['=', 'recoveries.created_by', Yii::$app->user->getId()])
                ->andFilterWhere(['is', 'cih_transactions_mapping.cih_type_id', new \yii\db\Expression('null')]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        /*echo'<pre>';
        print_r($dataProvider->getModels());
        die();*/
        if (isset($params['region_id']) && $params['region_id'] != null) {
            $query->andFilterWhere(['=', 'region_id', $params['region_id']]);
        }
        if (isset($params['area_id']) && $params['area_id'] != null) {
            $query->andFilterWhere(['=', 'area_id', $params['area_id']]);
        }
        if (isset($params['id']) && $params['id'] != null) {
            $query->andFilterWhere(['=', 'branch_id', $params['id']]);
        }
        if (isset($params['receipt_no']) && $params['receipt_no'] != null) {
            $query->andFilterWhere(['=', 'receipt_no', $params['receipt_no']]);
        }
        if (isset($params['amount']) && $params['amount'] != null) {
            $query->andFilterWhere(['=', $this->table_name.'.'.'amount', $params['amount']]);
        }

        if (isset($params['receive_date']) && !is_null($params['receive_date']) && strpos($params['receive_date'], ' - ') !== false) {
            $date = explode(' - ', $params['receive_date']);
            $query->andFilterWhere(['between', 'receive_date',strtotime(date('Y-m-d 00:00:00',strtotime($date[0]))), strtotime(date('Y-m-d 23:59:59',strtotime($date[1])))]);
        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }
}
