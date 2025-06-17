<?php

namespace common\models\search;

use common\models\SchedulesKpp;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SchedulesSearch represents the model behind the search form about `common\models\Schedules`.
 */
class SchedulesKppSearch extends SchedulesKpp
{
    public $sanction_no;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'application_id', 'loan_id', 'branch_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['due_date', 'created_at', 'updated_at','sanction_no'], 'safe'],
            [['schdl_amnt', 'overdue', 'overdue_log', 'advance', 'advance_log', 'due_amnt', 'credit'], 'number'],
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
        $query = SchedulesKpp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $this->load($params);
        $dataProvider->pagination->pageSize=50;
        $query->joinWith('loan');
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->setSort([
            'attributes' => [

                'due_date' => [
                    'asc' => ['due_date' => SORT_ASC],
                    'desc' => ['due_date' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'schdl_amnt' => [
                    'asc' => ['schdl_amnt' => SORT_ASC],
                    'desc' => ['schdl_amnt' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['loans.sanction_no' => SORT_ASC],
                    'desc' => ['loans.sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],

            ]
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
            'loans.application_id' => $this->application_id,
            'loan_id' => $this->loan_id,
            'loans.branch_id' => $this->branch_id,
            'due_date' => $this->due_date,
            'schdl_amnt' => $this->schdl_amnt,
            'overdue' => $this->overdue,
            'overdue_log' => $this->overdue_log,
            'advance' => $this->advance,
            'advance_log' => $this->advance_log,
            'due_amnt' => $this->due_amnt,
            'credit' => $this->credit,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['loans.sanction_no' =>  $this->sanction_no]);



        return $dataProvider;
    }
}
