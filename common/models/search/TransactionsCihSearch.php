<?php

namespace common\models\search;

use common\models\CihTransactionsMapping;
use common\models\Donations;
use common\models\Transactions;
use common\models\TransactionsCih;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TransactionsCih represents the model behind the search form of `common\models\TransactionsCih`.
 */
class TransactionsCihSearch extends Transactions
{
    public $branch_name;
    public $count;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'area_id', 'branch_id', 'account_id', 'created_by' ], 'integer'],
            [['type', 'deposit_slip_no', 'deposit_date', 'deposited_by', 'created', 'updated','status','branch_name','count'], 'safe'],
            [['amount', 'tax'], 'number'],
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
    public function search($params)
    {
        $query = Transactions::find()/*->where(['status'=>'New'])*/;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        //$query->joinWith('branch');
        $dataProvider->setSort([
            'attributes' => [
                'branch_name' =>[
                    'asc'=> ['branches.name'=> SORT_ASC],
                    'desc'=> ['branches.name'=> SORT_DESC],
                    'default'=> ['branches.name'=> SORT_ASC],
                ],
                'title' =>[
                    'asc'=> ['title'=> SORT_ASC],
                    'desc'=> ['title'=> SORT_DESC],
                    'default'=> ['title'=> SORT_ASC],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'branch_id' => $this->branch_id,
            'amount' => $this->amount,
            'tax' => $this->tax,
            'account_id' => $this->account_id,
            'deposit_date' => $this->deposit_date,
            'created_by' => $this->created_by,
            'status' => $this->status,
        ]);

      //  $query->andFilterWhere(['like', 'title', $this->title])
            $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'deposit_slip_no', $this->deposit_slip_no])
             ->andFilterWhere(['like', 'branches.name', $this->branch_name])
            ->andFilterWhere(['like', 'deposited_by', $this->deposited_by]);

        return $dataProvider;
    }
    public function searchdeposit($params,$strt_date,$end_date)
    {
       // print_r( $params);
       // die();
        $query = Transactions::find()/*->where(['status'=>'New'])*/;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=50;
        //$query->joinWith('branch');
        $dataProvider->setSort([
            'attributes' => [
                'branch_name' =>[
                    'asc'=> ['branches.name'=> SORT_ASC],
                    'desc'=> ['branches.name'=> SORT_DESC],
                    'default'=> ['branches.name'=> SORT_ASC],
                ],
                'title' =>[
                    'asc'=> ['title'=> SORT_ASC],
                    'desc'=> ['title'=> SORT_DESC],
                    'default'=> ['title'=> SORT_ASC],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
          'id' =>$this->id,

            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'branch_id' => $this->branch_id,
            'amount' => $this->amount,
           'tax' => $this->tax/*count('id.cih.cih_type_id')*/,
            'account_id' => $this->account_id,
            'deposit_date' => $this->deposit_date,
            'created_by' => $this->created_by,
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['between', 'deposit_date', strtotime($strt_date), strtotime($end_date)]);
        $query->andFilterWhere(['between', 'amount', isset($params['amnt_frm'])?$params['amnt_frm']:0, isset($params['amnt_to'])?$params['amnt_to']:10000000]);

        if(isset($params['receipt'])){
            $query->andFilterWhere([ 'deposit_slip_no'=> trim($params['receipt'])]);
        }

        $query->andFilterWhere(['like', 'type', $this->type])
            //->andFilterWhere(['like', 'deposit_slip_no', $this->deposit_slip_no])
            ->andFilterWhere(['like', 'branches.name', $this->branch_name])
            ->andFilterWhere(['like', 'deposited_by', $this->deposited_by]);

        foreach ($dataProvider->getModels() as $dp)
        {
            $cont=CihTransactionsMapping::find()->where(['transaction_id'=>$dp['id']])->all();
          $as=  count($cont);
            $dp['tax']=$as;
        }

        return $dataProvider;

    }
}
