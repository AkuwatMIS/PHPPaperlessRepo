<?php

namespace common\models\search;

use common\models\Applications;
use common\models\Members;
use common\models\RejectedNadraVerisys;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CitiesSearch represents the model behind the search form about `common\models\Cities`.
 */
class KamyabPakistanSearch extends Applications
{
    /**
     * @inheritdoc
     */

    public $cnic_issue_date;
    public $cnic_expiry_date;
    public $full_name;
    public $parentage;
    public $nadra_verisys_status;
    //  public $cnic;
    //public $account_type;

    public function rules()
    {
        return [
            [['id', 'region_id', 'assigned_to', 'created_by', 'updated_by', 'project_id', 'area_id', 'branch_id'], 'integer'],
            [['name', 'created_at', 'updated_at', 'cnic_expiry_date', 'cnic_issue_date', 'cnic', 'application_date', 'nadra_verisys_status'], 'safe'],
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
        $rejected_arr = [];
        $rejected = RejectedNadraVerisys::find()->select('application_id')->where(['status' => [0, 1]])->all();
        foreach ($rejected as $reject) {
            $rejected_arr[] = $reject['application_id'];
        }
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('member');
        $query->joinWith('member.info');
        if (!empty($params['KamyabPakistanSearch']['cnic'])) {
            $query->joinWith('member.nadraDoc');
        }
        if ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'yes') {
            $query->joinWith('member.nadraDoc');
        }
        if ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'no') {
            $query->joinWith('member.nadraDoc');
        }

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        if ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'no') {
            $query->where(['is', 'images.parent_id', null]);
        }



        $query->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'member_info.cnic_expiry_date' => $this->cnic_expiry_date,
            'member_info.cnic_issue_date' => $this->cnic_issue_date,
        ]);

        $query->andFilterWhere(['=', 'members.full_name', $this->full_name])
            ->andFilterWhere(['=', 'members.parentage', $this->parentage])
            ->andFilterWhere(['=', 'members.cnic', $this->cnic])
            ->andFilterWhere(['applications.deleted' => 0])
            ->andFilterWhere(['not in', 'applications.id', $rejected_arr]);

                if(!empty($this->application_date)) {
                    $date_range = explode(' - ',$this->application_date);
                    $date_range_1 = strtotime(date('Y-m-d-00:00', strtotime($date_range[0])));
                    $date_range_2 = strtotime(date('Y-m-d-23:59', strtotime($date_range[1])));

                    $query->andFilterWhere(['between', 'applications.created_at', $date_range_1, $date_range_2]);
                } else if(!empty($this->created_at)) {
                    $query->andFilterWhere(['between', 'applications.created_at', strtotime($this->created_at), strtotime('+23 hour +59 minutes +59 seconds', strtotime($this->created_at))]);

                }

        if ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'yes') {
            $query->andFilterWhere(['=', 'images.image_type', 'nadra_document']);
        }

        if (!empty($this->project_id)) {
            $query->andFilterWhere(['=', 'project_id', $this->project_id]);
        }

        $app_date_check = date('01-06-2022');
        $query->andFilterWhere(['>=', 'applications.created_at', strtotime($app_date_check)]);

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchSummary($params, $export = false)
    {
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('member.nadraDoc');


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        $query->where(['is', 'images.parent_type', 'members']);
//        $query->where(['is', 'images.image_type', 'nadra_document']);
        $query->where(['is', 'images.parent_id', null]);

        $query->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications.deleted' => 0,
        ]);

        if (!is_null($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            $date = explode(' - ', $this->created_at);
            $query->andFilterWhere(['between', 'applications.created_at', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['between', 'applications.created_at', strtotime(date('d-m-y')), strtotime('+23 hour +59 minutes +59 seconds', strtotime(date('d-m-y')))]);
        }

        $result['unverified'] = count($query->all());

        $query2 = Applications::find();

        $query2->joinWith('member.nadraDoc');


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider2;
        }

        $query2->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications.deleted' => 0,
        ]);

        $query2->andFilterWhere(['=', 'images.image_type', 'nadra_document']);

        if (!is_null($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            $date = explode(' - ', $this->created_at);
            $query2->andFilterWhere(['between', 'applications.created_at', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query2->andFilterWhere(['between', 'applications.created_at', strtotime(date('d-m-y')), strtotime('+23 hour +59 minutes +59 seconds', strtotime(date('d-m-y')))]);
        }
        $result['verified'] = count($query2->all());

        return $result;

    }

    public function searchNadra($params, $export = false)
    {
        $rejected_arr = [];
        $rejected = RejectedNadraVerisys::find()->select('application_id')->where(['status' => [0, 1]])->all();
        foreach ($rejected as $reject) {
            $rejected_arr[] = $reject['application_id'];
        }
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('member');
        $query->joinWith('nadra');
        $query->joinWith('member.info');

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        $query->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.deleted' => 0,
            'member_info.cnic_expiry_date' => $this->cnic_expiry_date,
            'member_info.cnic_issue_date' => $this->cnic_issue_date,
            'members.full_name' => $this->full_name,
            'members.parentage' => $this->parentage,
            'members.cnic' => $this->cnic,
        ]);

        $query->andWhere(['not in', 'applications.id', $rejected_arr]);

        if(!empty($this->application_date)) {
            $date_range = explode(' - ',$this->application_date);
            $date_range_1 = strtotime(date('Y-m-d-00:00', strtotime($date_range[0])));
            $date_range_2 = strtotime(date('Y-m-d-23:59', strtotime($date_range[1])));

            $query->andWhere(['between', 'nadra_verisys.upload_at', $date_range_1, $date_range_2]);
        }
        $query->andWhere(['>', 'nadra_verisys.upload_at', 0]);

        if ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'yes') {
            $query->andWhere(['=', 'nadra_verisys.status', 1]);
        }elseif ($params['KamyabPakistanSearch']['nadra_verisys_status'] == 'no') {
            $query->andWhere(['=', 'nadra_verisys.status', 0]);
        }

        if (!empty($this->project_id)) {
            $query->andWhere(['=', 'project_id', $this->project_id]);
        }

        $app_date_check = date('01-06-2022');
        $query->andWhere(['>=', 'applications.created_at', strtotime($app_date_check)]);

//        echo $query->createCommand()->getRawSql();
//        die();

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    public function searchNadraSummary($params, $export = false)
    {
        $query = Applications::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('nadra');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['=', 'nadra_verisys.status', 0]);

        $query->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications.deleted' => 0
        ]);
        $query->andFilterWhere(['in', 'applications.status', ['pending','approved']]);

        if (!is_null($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            $date = explode(' - ', $this->created_at);
            $query->andFilterWhere(['between', 'applications.created_at', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query->andFilterWhere(['between', 'applications.created_at', strtotime(date('d-m-y')), strtotime('+23 hour +59 minutes +59 seconds', strtotime(date('d-m-y')))]);
        }

        $result['unverified'] = count($query->all());

        $query2 = Applications::find();
        $query2->joinWith('nadra');

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider2;
        }

        $query2->andFilterWhere([
            'id' => $this->id,
            'applications.region_id' => $this->region_id,
            'applications.area_id' => $this->area_id,
            'applications.branch_id' => $this->branch_id,
            'applications.project_id' => $this->project_id,
            'applications.deleted' => 0,
        ]);
        $query->andFilterWhere(['=', 'nadra_verisys.status', 1]);

        if (!is_null($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            $date = explode(' - ', $this->created_at);
            $query2->andFilterWhere(['between', 'applications.created_at', strtotime($date[0]), strtotime(($date[1] . ' 23:59:59'))]);
        } else {
            $query2->andFilterWhere(['between', 'applications.created_at', strtotime(date('d-m-y')), strtotime('+23 hour +59 minutes +59 seconds', strtotime(date('d-m-y')))]);
        }
        $result['verified'] = count($query2->all());

        return $result;

    }
}
