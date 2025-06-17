<?php

namespace common\models\search;

use common\models\Applications;
use common\models\Groups;
use common\models\Loans;
use common\models\Members;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

class StepsSearch extends Applications
{
    public $cnic;
    public $cibstatus;
    public $Nadra;
    public $PMT;
    public $full_name;
    public $Account_Verification;
    public $region;
    public $area;
    public $branch;
    public $project;
    public $status;

    public function rules()
    {
        return [
            [['id', 'area_id', 'branch_id', 'region_id', 'project_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
           [['status'], 'safe'],
            [['full_name', 'cnic', 'region', 'area', 'branch', 'project','application_date','cibstatus','Nadra','PMT','Account_Verification'], 'safe'],
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
    public function search($params,$export = false)
    {
        $query = Applications::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('cib');
        $query->joinWith('member');
        $query->joinWith('pmtStatus');
        $query->joinWith('loan.accountVerification');
        $query->joinWith('member.nadraDoc');


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if(!empty($params['StepsSearch']['Nadra'])) {

            if ($params['StepsSearch']['Nadra'] == 'No') {
                $query->where(['is', 'images.parent_id', null]);
            }
            if ($params['StepsSearch']['Nadra'] == 'Yes') {
                $query->andFilterWhere(['=', 'images.image_type', 'nadra_document']);
            }
        }

        $query->andFilterWhere([
            'applications_cib.status' => $this->cibstatus,
            'applications.region_id' => $this->region_id,
            'loan_actions.status' => $this->Account_Verification,
            'application_details.status' =>$this->PMT,
            'applications.status' =>$this->status,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications.deleted' => 0,
            'members.full_name' => $this->full_name,
            'members.cnic' => $this->cnic,
            'created_at' => $this->created_at
        ]);

        $query->andFilterWhere(['!=','applications.status','rejected'])
          ->andFilterWhere(['>','applications.application_date','1638298800']);


        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }
}

