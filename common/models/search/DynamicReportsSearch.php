<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DynamicReports;

/**
 * DynamicReportsSearch represents the model behind the search form about `common\models\DynamicReports`.
 */
class DynamicReportsSearch extends DynamicReports
{
    /**
     * @inheritdoc
     */

    public $report_name;
    public $user_name;
    public $description;

    public function rules()
    {
        return [
            [['id', 'report_defination_id', 'created_by', 'created_at'], 'integer'],
            [['sql_filters', 'visibility', 'notification', 'status','report_name','user_name'], 'safe'],
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

        $query = DynamicReports::find();
        $query->joinWith('report');
        $query->joinWith('user');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        /*$dataProvider->setSort([
            'attributes' => [
                'report_name' => [
                    'asc' => ['report_definations.name' => SORT_ASC],
                    'desc' => ['report_definations.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'user_name' => [
                    'asc' => ['users.username' => SORT_ASC],
                    'desc' => ['users.username' => SORT_DESC],
                    'default' => SORT_ASC
                ]
            ]
        ]);*/

        $query->andFilterWhere([
            'id' => $this->id,
            //'report_defination_id' => $this->report_defination_id,
            'dynamic_reports.created_by' => Yii::$app->user->getId(),
            'created_at' => $this->created_at,
            'dynamic_reports.deleted'=>0,
        ]);

        $query->andFilterWhere(['like', 'sql_filters', $this->sql_filters])
            ->andFilterWhere(['like', 'visibility', $this->visibility])
            ->andFilterWhere(['like', 'notification', $this->notification])
            ->andFilterWhere(['like', 'report_definations.name', $this->report_name])
            ->andFilterWhere(['like', 'users.username', $this->user_name])
            ->andFilterWhere(['like', 'dynamic_reports.status', $this->status])
            ->andFilterWhere(['in', 'dynamic_reports.report_defination_id', $this->report_defination_id])
            //->andFilterWhere(['like', 'dynamic_reports.is_approved', $this->is_approved]);
            ->andFilterWhere(['=', 'dynamic_reports.created_by', Yii::$app->user->getId()]);

        $query->orderBy('dynamic_reports.created_at desc');
        return $dataProvider;
    }
    public function search_approval_list($params)
    {

        $query = DynamicReports::find();
        $query->joinWith('report');
        $query->joinWith('user');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        /*$dataProvider->setSort([
            'attributes' => [
                'report_name' => [
                    'asc' => ['report_definations.name' => SORT_ASC],
                    'desc' => ['report_definations.name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'user_name' => [
                    'asc' => ['users.username' => SORT_ASC],
                    'desc' => ['users.username' => SORT_DESC],
                    'default' => SORT_ASC
                ]
            ]
        ]);*/

        $query->andFilterWhere([
            'id' => $this->id,
            //'report_defination_id' => $this->report_defination_id,
            //'dynamic_reports.created_by' => Yii::$app->user->getId(),
            'created_at' => $this->created_at,
            'dynamic_reports.deleted'=>0,
            'dynamic_reports.is_approved'=>0,
        ]);

        $query->andFilterWhere(['like', 'sql_filters', $this->sql_filters])
            ->andFilterWhere(['like', 'visibility', $this->visibility])
            ->andFilterWhere(['like', 'notification', $this->notification])
            ->andFilterWhere(['like', 'report_definations.name', $this->report_name])
            ->andFilterWhere(['like', 'users.username', $this->user_name])
            ->andFilterWhere(['like', 'dynamic_reports.status', $this->status])
            ->andFilterWhere(['in', 'dynamic_reports.report_defination_id', $this->report_defination_id]);
            //->andFilterWhere(['like', 'dynamic_reports.is_approved', $this->is_approved]);
           // ->andFilterWhere(['=', 'dynamic_reports.created_by', Yii::$app->user->getId()]);

        $query->orderBy('dynamic_reports.created_at desc');
        return $dataProvider;
    }
}
