<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AwpRecoveryPercentage;

/**
 * AwpRecoveryPercentageSearch represents the model behind the search form of `common\models\AwpRecoveryPercentage`.
 */
class AwpRecoveryPercentageSearch extends AwpRecoveryPercentage
{
    /**
     * {@inheritdoc}
     */
    public $month_from;
    public function rules()
    {
        return [
            [['id', 'branch_id', 'area_id', 'region_id', 'branch_code', 'recovery_count', 'recovery_one_to_ten', 'recovery_eleven_to_twenty', 'recovery_twentyone_to_thirty', 'created_at', 'updated_at'], 'integer'],
            [['month','month_from'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = AwpRecoveryPercentage::find()
            ->select(['branch_id as id','region_id','area_id','branch_id','sum(recovery_count) as recovery_count','sum(recovery_one_to_ten) as recovery_one_to_ten','sum(	recovery_eleven_to_twenty) as 	recovery_eleven_to_twenty'
                ,'sum(recovery_twentyone_to_thirty) as recovery_twentyone_to_thirty']);
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'branch_id' => $this->branch_id,
            'area_id' => $this->area_id,
            'region_id' => $this->region_id,
            /*'branch_code' => $this->branch_code,
            'recovery_count' => $this->recovery_count,
            'recovery_one_to_ten' => $this->recovery_one_to_ten,
            'recovery_eleven_to_twenty' => $this->recovery_eleven_to_twenty,
            'recovery_twentyone_to_thirty' => $this->recovery_twentyone_to_thirty,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,*/
        ]);
        if(empty($params['AwpRecoveryPercentageSearch']['month']) && empty($params['AwpRecoveryPercentageSearch']['month_from']))
        {
            $query->andFilterWhere(['between', 'awp_recovery_percentage.month','2022-07', '2023-06']);
        } else {
            if($this->month_from>=1 && $this->month_from<=6){
                $this->month_from='2023-'.$this->month_from;
            }
            else{
                $this->month_from='2022-'.$this->month_from;
            }
            if($this->month>=1 && $this->month<=6){
                $this->month='2023-'.$this->month;
            }
            else{
                $this->month='2022-'.$this->month;
            }
            $query->andFilterWhere(['between', 'awp_recovery_percentage.month', $this->month_from, $this->month]);
        }
        $query->groupBy('branch_id');

        return $dataProvider;
    }
}
