<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApplicationsCib;

/**
 * ApplicationsCibSearch represents the model behind the search form about `common\models\ApplicationsCib`.
 */
class ApplicationsCibSearch extends ApplicationsCib
{
    /**
     * @inheritdoc
     */


    public function rules()
    {
        return [
            [['id', 'application_id', 'cib_type_id', 'status', 'created_by', 'created_at','project_id'], 'integer'],
            [['fee'], 'number'],
            [['dob','receipt_no', 'type', 'file_path', 'response','region_id','area_id','branch_id','member_cnic','updated_at','req_amount','city_id','gender','address','app_date','cib_date','app_status','member_name','parentage','app_no'], 'safe'],
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
     * @return \yii\db\ActiveQuery
     */
    public function search($params,$export=false)
    {

        $query = ApplicationsCib::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->joinWith('application.member');
        $query->joinWith('application.region');
        $query->joinWith('application.area');
        $query->joinWith('application.branch');
        $query->joinWith('application.projects');

        $query->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'members.cnic' => $this->member_cnic,
            'application_id' => $this->application_id,
            'applications_cib.cib_type_id' => $this->cib_type_id,
            'applications_cib.fee' => $this->fee,
            'applications_cib.receipt_no' => $this->receipt_no,
            'applications_cib.type' => $this->type,
            'applications_cib.status' => 1,
            'applications_cib.created_by' => $this->created_by,
            'applications_cib.created_at' => $this->created_at,
           // 'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
            ->andFilterWhere(['=', 'applications_cib.type', 0])
            ->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'response', $this->response]);

        if (!is_null($this->updated_at) && strpos($this->updated_at, ' - ') !== false) {
            $date = explode(' - ', $this->updated_at);
            $query->andFilterWhere(['between', 'applications_cib.updated_at', strtotime($date[0].' - 5 hours'), strtotime($date[1].' - 5 hours')]);
        } else {
            //$query->andFilterWhere(['applications_cib.updated_at' => $this->updated_at]);
            $first_day_this_month = date('m-01-Y');
            $query->andFilterWhere(['between', 'applications_cib.updated_at', strtotime($first_day_this_month.' - 5 hours'), strtotime("last day of this month".' - 5 hours')]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }


    public function searchAdvance($params,$export=false)
    {

        $query = ApplicationsCib::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('application.member');
        $query->joinWith('application.region');
        $query->joinWith('application.area');
        $query->joinWith('application.branch');
        $query->joinWith('application.project');
        $query->joinWith('application.member.membersAddresses');
        $query->joinWith('application.branch.city');

        $query->andFilterWhere([
            'applications_cib.id' => $this->id,
            'applications_cib.application_id' => $this->application_id,
            'members.cnic' => $this->member_cnic,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications_cib.cib_type_id' => $this->cib_type_id,
            'applications_cib.fee' => $this->fee,
            'applications_cib.receipt_no' => $this->receipt_no,
            'applications_cib.type' => $this->type,
            'applications_cib.status' => $this->status
        ]);
        $query->groupBy(['applications_cib.application_id']);
        if (!is_null($this->updated_at) && strpos($this->updated_at, ' - ') !== false) {
            $date = explode(' - ', $this->updated_at);
            $query->andFilterWhere(['between', 'applications_cib.updated_at', strtotime($date[0].' - 5 hours'), strtotime($date[1].' - 5 hours')]);
        }

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }
}
