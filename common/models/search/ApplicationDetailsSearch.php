<?php

namespace common\models\search;

use common\models\ApplicationDetails;
use common\models\Loans;
use common\models\Users;
use common\models\Visits;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Applications;

/**
 * ApplicationsSearch represents the model behind the search form about `common\models\Applications`.
 */
class ApplicationDetailsSearch extends ApplicationDetails
{
    public $full_name;
    public $cnic;
    public $region;
    public $area;
    public $branch;
    public $application_id;
    public $project_id;
    //public $poverty_score;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'parent_type'], 'required'],
            [['created_at', 'updated_at', 'status', 'poverty_score', 'is_shifted', 'application_id'], 'safe'],
            [['full_name', 'cnic', 'region', 'area', 'branch','project_id','action_date'], 'safe']
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
    public function search($params, $export = false)
    {
        $project_ids = [77,78,79,105,106,132];
        $query = ApplicationDetails::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'pagination' => false,
        ]);

        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('application');
        $query->joinWith('application.member');

        $this->load($params);


//        if (!$this->validate()) {
//            print_r($this->application_id);
//            die();
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//
//            return $dataProvider;
//        }

        $query->andFilterWhere([
            'application_details.application_id' => $this->application_id,
            'application_details.status' => $this->status,
            //'application_details.action_date' => $this->action_date,
            'application_details.parent_type' => 'member',
            'application_details.poverty_score' => $this->poverty_score,
            'application_details.deleted' => 0,
            'applications.deleted'=>0,
            'applications.project_id' => $this->project_id,
            'members.cnic' => $this->cnic,
            'members.full_name' => $this->full_name,
            'applications.branch_id' => $this->branch,

        ]);

        $query->andFilterWhere(['applications.project_id'=>$project_ids])
            ->andFilterWhere(['!=','applications.status','rejected']);
      //      if(isset($params['ApplicationDetailsSearch']['action_date'])){
      //          $query->andFilterWhere(['=','application_details.action_date',strtotime($params['ApplicationDetailsSearch']['action_date'])]);

       //     }
      // ->andFilterWhere(['=','application_details.action_date',date('Y-m-d H:i:s')]);
//        $query->andFilterWhere(['like', 'members.full_name', $this->full_name])
//            ->andFilterWhere(['like','branches.name',$this->branch])
//            ->andFilterWhere(['like','regions.name',$this->region])
//            ->andFilterWhere(['like','areas.name',$this->area]);

        $query->orderBy('created_at desc');
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

}
