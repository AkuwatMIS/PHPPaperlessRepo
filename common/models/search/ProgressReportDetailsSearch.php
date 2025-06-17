<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProgressReportDetails;

/**
 * ProgressReportDetailsSearch represents the model behind the search form about `common\models\ProgressReportDetails`.
 */
class ProgressReportDetailsSearch extends ProgressReportDetails
{
    public $project_id;
    public $report_date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'progress_report_id', 'division_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'country_id', 'province_id', 'district_id', 'city_id', 'no_of_loans', 'family_loans', 'female_loans', 'active_loans', 'cum_disb', 'cum_due', 'cum_recv', 'overdue_borrowers', 'overdue_amount', 'par_amount', 'not_yet_due', 'olp_amount', 'cih', 'mdp', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['branch_code', 'gender', 'deleted','project_id'], 'safe'],
            [['overdue_percentage', 'par_percentage', 'recovery_percentage'], 'number'],
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
       /* $query = ProgressReportDetails::find();

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
            'progress_report_id' => $this->progress_report_id,
            'division_id' => $this->division_id,
            'region_id' => $this->region_id,
            'area_id' => $this->area_id,
            'branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'field_id' => $this->field_id,
            'country_id' => $this->country_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'city_id' => $this->city_id,
            'no_of_loans' => $this->no_of_loans,
            'family_loans' => $this->family_loans,
            'female_loans' => $this->female_loans,
            'active_loans' => $this->active_loans,
            'cum_disb' => $this->cum_disb,
            'cum_due' => $this->cum_due,
            'cum_recv' => $this->cum_recv,
            'overdue_borrowers' => $this->overdue_borrowers,
            'overdue_amount' => $this->overdue_amount,
            'overdue_percentage' => $this->overdue_percentage,
            'par_amount' => $this->par_amount,
            'par_percentage' => $this->par_percentage,
            'not_yet_due' => $this->not_yet_due,
            'olp_amount' => $this->olp_amount,
            'recovery_percentage' => $this->recovery_percentage,
            'cih' => $this->cih,
            'mdp' => $this->mdp,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'branch_code', $this->branch_code])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'deleted', $this->deleted]);

        return $dataProvider;*/
        $query = ProgressReportDetails::find();

        $query->joinWith('progress');
        //$query->joinWith('region');
        //$query->joinWith('area');
        //$query->joinWith('branch');
        $query->joinWith('progress.project');

        /*echo '<pre>';
        print_r($params);
        die();*/
        $this->load($params);
        Yii::$app->Permission->searchProgressReportsFilters($query,'frontend');
        //RbacHelper::searchProgressReportsFilters($query);
        /*echo '<pre>';
        print_r($this);
        die();*/
        
        $query->andFilterWhere([
            'id' => $this->id,
            'progress_report_id' => $this->progress_report_id,
            'division_id' => $this->division_id,
            'progress_report_details.region_id' => $this->region_id,
            'progress_report_details.area_id' => $this->area_id,
            'progress_report_details.branch_id' => $this->branch_id,
            'progress_report_details.team_id' => $this->team_id,
            'progress_report_details.field_id' => $this->field_id,

            'no_of_loans' => $this->no_of_loans,
            'family_loans' => $this->family_loans,
            'female_loans' => $this->female_loans,
            'active_loans' => $this->active_loans,
            'cum_disb' => $this->cum_disb,
            'cum_due' => $this->cum_due,
            'cum_recv' => $this->cum_recv,
            'overdue_borrowers' => $this->overdue_borrowers,
            'overdue_amount' => $this->overdue_amount,
            'overdue_percentage' => $this->overdue_percentage,
            'par_amount' => $this->par_amount,
            'par_percentage' => $this->par_percentage,
            'not_yet_due' => $this->not_yet_due,
            'olp_amount' => $this->olp_amount,
            'recovery_percentage' => $this->recovery_percentage,
            'cih' => $this->cih,
            'progress_report_details.mdp' => $this->mdp,
        ]);

        $query->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'division', $this->division])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'city', $this->city])
            //->andFilterWhere(['like', 'DATE_FORMAT(progress_reports.report_date,\'%Y-%m-%d\')', $this->report_date])
            //->andFilterWhere(['=', 'progress_reports.id', $this->project_id]);
            ->andFilterWhere(['<>', 'no_of_loans', 0]);
        $query->orderBy([
            'region_id' => SORT_ASC,
            'area_id'=>SORT_ASC,
            'branch_id'=>SORT_ASC
        ]);
        /*print_r($params);
        print_r($this);*/
        /*echo '<pre>';
        print_r($query->asArray()->all());
        die();*/
        return $query->asArray()->all();
        //return $dataProvider;
    }
}
