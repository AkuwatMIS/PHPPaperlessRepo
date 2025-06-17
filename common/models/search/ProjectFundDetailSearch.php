<?php

namespace common\models\search;

use common\models\LoanTranches;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProjectFundDetail;

/**
 * ProjectFundDetailSearch represents the model behind the search form about `common\models\ProjectFundDetail`.
 */
class ProjectFundDetailSearch extends ProjectFundDetail
{
    /**
     * @inheritdoc
     */

    public $txn_mode;
    public $sanction_no;
    public $cnic;
    public $received_at;
    public $name;

    public function rules()
    {
        return [
            [['id', 'project_id', 'fund_batch_amount',  'no_of_loans', 'created_at', 'updated_at'], 'integer'],
            [['status','batch_no','txn_mode','txn_no','credit','debit','allocation_date','disbursement_source'], 'safe'],
            [['sanction_no','cnic','name','received_at'], 'safe'],
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
    public function search($params , $export=false)
    {
        $query = ProjectFundDetail::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->joinWith('fund');
        $query->joinWith('transaction');

        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'fund_batch_amount' => $this->fund_batch_amount,
            'batch_no' => $this->batch_no,
            'no_of_loans' => $this->no_of_loans,
            //'allocation_date' => strtotime($this->allocation_date),
            'disbursement_source' => $this->disbursement_source,
            'funds.name' => $this->name,
            //'transactions.received_at' => strtotime($this->received_at),
            //'received_via' => $this->received_via,
            //'receive_date' => $this->receive_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if(!empty($this->allocation_date)){
            $query->andFilterWhere(['allocation_date' => strtotime($this->allocation_date)]);
        }
        if(!empty($this->received_at)){
            $query->andFilterWhere(['transactions.received_at' => strtotime($this->received_at)]);
        }
        $query->andFilterWhere(['like', 'status', $this->status]);

        if($export){
            return $query;
        }
        return $dataProvider;
    }

    public function searchList($id,$params)
    {

        $query = LoanTranches::find()->where(['batch_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(isset($params['ProjectFundDetailSearch']['sanction_no']) || !empty($params['ProjectFundDetailSearch']['sanction_no'])){
            $query->joinWith('loan');
            $query->andFilterWhere(['loans.sanction_no' => $params['ProjectFundDetailSearch']['sanction_no']]);
        }

        if(isset($params['ProjectFundDetailSearch']['cnic']) || !empty($params['ProjectFundDetailSearch']['cnic'])){
            $query->joinWith('loan.application.member');
            $query->andFilterWhere(['members.cnic' => $params['ProjectFundDetailSearch']['cnic']]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
