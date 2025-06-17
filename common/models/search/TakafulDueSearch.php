<?php

namespace common\models\search;

use common\models\Loans;
use common\models\Operations;
use common\models\reports\TakafulDueList;
use common\models\Takafuldue;
use yii\base\Model;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

class TakafulDueSearch extends Loans
{

    public $branch_id;
    public $area;
    public $region;
    public $branch;
    public $area_id;
    public $region_id;
    public $name;
    public $parentage;
    public $cnic;
    public $address;
    public $mobile;
    public $takaful;
    public $olp;
    public $Due_Date;
    public $Disburse_Date;
    public $overdue;

    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'branch_id', 'area_id', 'region_id', 'created_by', 'project_ids'], 'integer'],
            [[ 'receive_date'], 'safe'],
            [['name', 'receive_date'], 'safe']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params, $export = false)
    {

        $query=Takafuldue::find()
            ->join('inner join','loans','loans.id=takaful_due.loan_id')
            ->where(['in', 'loans.status', ['collected','loan completed']])
            ->andWhere(['in', 'loans.project_id', [77,78,79]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize = 50;
        $this->load($params);


        $query->andFilterWhere([
            'takaful_due.region_id' => $this->region_id,
            'takaful_due.area_id' => $this->area_id,
            'takaful_due.branch_id' => $this->branch_id,
            'takaful_due.status'=>0
        ]);
        if(isset($params['TakafulDueSearch']['Due_Date']) && !empty($params['TakafulDueSearch']['Due_Date']))
        {
            $startDate = strtotime(date("Y-m-01", strtotime($params['TakafulDueSearch']['Due_Date'])));
            $endDate   = strtotime(date("Y-m-t",strtotime($params['TakafulDueSearch']['Due_Date'])));

            $query->andFilterWhere(['between', 'overdue_date', $startDate, $endDate]);

        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
    }


}