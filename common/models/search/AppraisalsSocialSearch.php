<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppraisalsSocial;

/**
 * AppraisalsSocialSearch represents the model behind the search form about `common\models\AppraisalsSocial`.
 */
class AppraisalsSocialSearch extends AppraisalsSocial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'application_id', 'land_size', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', 'date_of_maturity', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['poverty_index', 'house_ownership', 'source_of_income', 'monthly_savings', 'other_loan', 'economic_dealings', 'social_behaviour', 'fatal_disease', 'description', 'description_image', 'social_appraisal_address', 'status', 'is_lock', 'deleted', 'platform'], 'safe'],
            [['house_rent_amount', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'amount', 'other_expenses', 'total_expenses', 'loan_amount', 'business_income', 'job_income', 'labour_income','agriculture_income','house_rent_income', 'other_income', 'expected_increase_in_income', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
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
        $query = AppraisalsSocial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'house_rent_amount' => $this->house_rent_amount,
            'land_size' => $this->land_size,
            'total_family_members' => $this->total_family_members,
            'no_of_earning_hands' => $this->no_of_earning_hands,
            'ladies' => $this->ladies,
            'gents' => $this->gents,
            'total_household_income' => $this->total_household_income,
            'utility_bills' => $this->utility_bills,
            'educational_expenses' => $this->educational_expenses,
            'medical_expenses' => $this->medical_expenses,
            'kitchen_expenses' => $this->kitchen_expenses,
            'amount' => $this->amount,
            'date_of_maturity' => $this->date_of_maturity,
            'other_expenses' => $this->other_expenses,
            'total_expenses' => $this->total_expenses,
            'loan_amount' => $this->loan_amount,
            'business_income' => $this->business_income,
            'job_income' => $this->job_income,
            'house_rent_income' => $this->house_rent_income,
            'other_income' => $this->other_income,
            'expected_increase_in_income' => $this->expected_increase_in_income,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'bm_verify_latitude' => $this->bm_verify_latitude,
            'bm_verify_longitude' => $this->bm_verify_longitude,
            'approved_by' => $this->approved_by,
            'approved_on' => $this->approved_on,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'poverty_index', $this->poverty_index])
            ->andFilterWhere(['like', 'house_ownership', $this->house_ownership])
            ->andFilterWhere(['like', 'source_of_income', $this->source_of_income])
            ->andFilterWhere(['like', 'monthly_savings', $this->monthly_savings])
            ->andFilterWhere(['like', 'other_loan', $this->other_loan])
            ->andFilterWhere(['like', 'economic_dealings', $this->economic_dealings])
            ->andFilterWhere(['like', 'social_behaviour', $this->social_behaviour])
            ->andFilterWhere(['like', 'fatal_disease', $this->fatal_disease])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'description_image', $this->description_image])
            ->andFilterWhere(['like', 'social_appraisal_address', $this->social_appraisal_address])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'is_lock', $this->is_lock])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'platform', $this->platform]);

        return $dataProvider;
    }
}
