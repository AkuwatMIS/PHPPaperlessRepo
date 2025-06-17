<?php

namespace common\models\search\nadra;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RejectedNadraVerisys;

/**
 * AnalyticsSearch represents the model behind the search form about `common\models\Analytics`.
 */
class RejectedNadraVerisysSearch extends RejectedNadraVerisys
{
    public $fullname;
    public $parentage;
    public $cnic;
    public $cnic_issue_date;
    public $cnic_expiry_date;
    public $project_id;
    public $branch_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id','member_info_id', 'rejected_date','branch_id'], 'required'],
            [['branch_id', 'area_id', 'region_id', 'status', 'reject_reason', 'remarks','cnic'], 'safe'],
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

    public function search($params,$export=false)
    {
             
        $query = RejectedNadraVerisys::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->joinWith('applications');
        $query->joinWith('info');
        $query->joinWith('applications.member');


        $dataProvider->pagination = ['pageSize' => 50];

        $this->load($params);
        $query->andFilterWhere([
            'rejected_nadra_verisys.branch_id'=>$this->branch_id,
            'rejected_nadra_verisys.area_id'=>$this->area_id,
            'rejected_nadra_verisys.region_id'=>$this->region_id,
            'rejected_nadra_verisys.status' => 0,
            'members.cnic'=>$this->cnic

        ]);

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

    public function searchRejectedReSubmit($params,$export=false)
    {
        $query = RejectedNadraVerisys::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('applications');
        $query->joinWith('info');
        $query->joinWith('applications.member');

        $dataProvider->pagination = ['pageSize' => 50];
        $query->andFilterWhere(['in', 'applications.status', ['pending','approved']]);
        $this->load($params);
        $query->andFilterWhere([
            'rejected_nadra_verisys.branch_id'=>$this->branch_id,
            'rejected_nadra_verisys.status' => 1,
            'members.cnic'=>$this->cnic
        ]);

        if($export){
            return $query;
        }else{
            return $dataProvider;
        }
    }

}
