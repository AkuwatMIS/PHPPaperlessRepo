<?php
namespace common\models\search;


use common\models\Applications;
use common\models\Members;
use common\models\RejectedNadraVerisys;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MemberCnicStatusSearch extends RejectedNadraVerisys{
        public $cnic;
        public $app_no;
        public $app_date;
        public $nadra_status;

        public function rules()
        {
                return [

                        [['cnic','app_no','app_date'], 'safe'],
                ];
        }
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


                $query = Applications::find();
                $query->joinWith('member');
                $dataProvider = new ActiveDataProvider([
                        'query' => $query,
                ]);
                $this->load($params);
                $query->andFilterWhere([
                        'members.cnic'=>$this->cnic
                ]);

                if ($export) {
                        return $query;
                } else {
                        return $dataProvider;
                }
        }

}