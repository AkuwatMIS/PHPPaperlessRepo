<?php

namespace common\models\search;

use common\models\reports\Overduelist;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class OverduelistSearch extends Overduelist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'loan_amount', 'inst_amnt', 'inst_months', 'disbursement_id', 'branch_id', 'area_id', 'region_id', 'br_serial', 'created_by', 'is_lock', 'project_id'], 'integer'],
            [['dateapprove', 'recovery', 'chequeno', 'acccode', 'inst_type', 'datedisburse', 'dateexpiry', 'cheque_dt', 'dsb_status', 'funding_line', 'loanexpiry', 'remarks', 'old_sanc_no', 'sanction_no', 'expiry_date', 'dt_entry'], 'safe'],
            [['due', 'overdue', 'balance'], 'number'],
            [['name', 'parentage', 'grpno', 'province_id', 'division_id', 'district_id', 'city_id', 'report_date'], 'safe']
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
        if (isset($params['OverduelistSearch']['report_date']) && !empty($params['OverduelistSearch']['report_date'])) {
                $date2 = $params['OverduelistSearch']['report_date'];
                $date1 = strtotime(date('Y-m-t', (strtotime($date2))));

                //$date = strtotime(date('Y-m-t', (strtotime('-1 months',($date1)))));
               // $date = strtotime(date("Y-m-t", ($date1)) . " -1 month");
                /* print_r($date.','.$date1);
                 die();*/
        }


        $query = Overduelist::find()->select('
            loans.id,loans.application_id,loans.sanction_no,members.full_name as name,members.parentage as parentage,
            loans.date_disbursed , loans.loan_amount,
            @amountapproved:=(loans.loan_amount),
            @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt),0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= "' . $date1 . '")) as schdl_amnt,
            @credit:=(select COALESCE(sum(recoveries.amount),0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= "' . $date1 . '")) as credit,
            (@schdl_amnt-@credit) as overdue_amount,
            (@amountapproved-@credit) as outstanding_balance,

        ');

//        @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt+schedules.charges_schdl_amount),0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= "' . $date1 . '")) as schdl_amnt,
//        @credit:=(select COALESCE(sum(recoveries.amount+recoveries.charges_amount),0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= "' . $date1 . '")) as credit,

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 100;
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'grpno' => [
                    'asc' => ['grpno' => SORT_ASC],
                    'desc' => ['grpno' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'credit' => [
                    'asc' => ['credit' => SORT_ASC],
                    'desc' => ['credit' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'overdue_amount' => [
                    'asc' => ['overdue_amount' => SORT_ASC],
                    'desc' => ['overdue_amount' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'outstanding_balance' => [
                    'asc' => ['outstanding_balance' => SORT_ASC],
                    'desc' => ['outstanding_balance' => SORT_DESC],
                    'default' => SORT_DESC
                ]
            ],
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
            //'project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'date_disbursed' => $this->date_disbursed,
            'cheque_dt' => $this->cheque_dt,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            //'loans.region_id' => $this->region_id,
            //'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            //'field_id' => $this->field_id,
            //'loan_expiry' => $this->loan_expiry,
            //'loan_completed_date' => $this->loan_completed_date,
            //'br_serial' => $this->br_serial,
            //'due' => $this->due,
            //'overdue' => $this->overdue,
            //'balance' => $this->balance,
            //'assigned_to' => $this->assigned_to,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,

            'loans.project_id' => $this->project_id,
            //'branches.province_id' => $this->province_id,
            //'branches.division_id' => $this->division_id,
            //'branches.district_id' => $this->district_id,
            //'branches.city_id' => $this->city_id,
            'loans.deleted' => 0,

        ]);
        $query->andFilterWhere(['=', 'cheque_no', $this->cheque_no]);
        $query->andFilterWhere(['=', 'inst_type', $this->inst_type])
            ->andFilterWhere(['<>', 'loans.status', 'not collected'])
            //->andFilterWhere(['<>', 'loans.status', 'loan completed'])
            //->andFilterWhere(['like', 'remarks', $this->remarks])
            //->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            //->andFilterWhere(['like', 'branch_id', $this->branch_id])
           // ->andFilterWhere(['like', 'project', $this->project])
            ->andFilterWhere(['=', 'members.full_name', $this->name])
            ->andFilterWhere(['=', 'members.parentage', $this->parentage])
            ->andFilterWhere(['=', 'grpno', $this->grpno]);

        $query->having('schdl_amnt - credit > 0');


        if ($export) {

            return $query;
        } else {

            return $dataProvider;
        }
    }

    public function searchCharges($params, $export = false)
    {
        if (isset($params['OverduelistSearch']['report_date']) && !empty($params['OverduelistSearch']['report_date'])) {
            $date2 = $params['OverduelistSearch']['report_date'];
            $date1 = strtotime(date('Y-m-t', (strtotime($date2))));

            //$date = strtotime(date('Y-m-t', (strtotime('-1 months',($date1)))));
            // $date = strtotime(date("Y-m-t", ($date1)) . " -1 month");
            /* print_r($date.','.$date1);
             die();*/
        }


        $query = Overduelist::find()->select('
            loans.id,loans.application_id,loans.sanction_no,members.full_name as name,members.parentage as parentage,
            loans.date_disbursed , loans.loan_amount,
            @amountapproved:=(loans.loan_amount),
            @schdl_amnt:=(select COALESCE(sum(schedules.charges_schdl_amount),0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= "' . $date1 . '")) as schdl_amnt,
            @credit:=(select COALESCE(sum(recoveries.charges_amount),0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= "' . $date1 . '")) as credit,
            (@schdl_amnt-@credit) as overdue_amount,
            (@amountapproved-@credit) as outstanding_balance,
        ');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 100;
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        //$query->joinWith('branch');
        //$query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'grpno' => [
                    'asc' => ['grpno' => SORT_ASC],
                    'desc' => ['grpno' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
                    'desc' => ['date_disbursed' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'credit' => [
                    'asc' => ['credit' => SORT_ASC],
                    'desc' => ['credit' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'overdue_amount' => [
                    'asc' => ['overdue_amount' => SORT_ASC],
                    'desc' => ['overdue_amount' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'outstanding_balance' => [
                    'asc' => ['outstanding_balance' => SORT_ASC],
                    'desc' => ['outstanding_balance' => SORT_DESC],
                    'default' => SORT_DESC
                ]
            ],
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
            //'project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'date_disbursed' => $this->date_disbursed,
            'cheque_dt' => $this->cheque_dt,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            //'loans.region_id' => $this->region_id,
            //'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            //'field_id' => $this->field_id,
            //'loan_expiry' => $this->loan_expiry,
            //'loan_completed_date' => $this->loan_completed_date,
            //'br_serial' => $this->br_serial,
            //'due' => $this->due,
            //'overdue' => $this->overdue,
            //'balance' => $this->balance,
            //'assigned_to' => $this->assigned_to,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,

            'loans.project_id' => $this->project_id,
            //'branches.province_id' => $this->province_id,
            //'branches.division_id' => $this->division_id,
            //'branches.district_id' => $this->district_id,
            //'branches.city_id' => $this->city_id,
            'loans.deleted' => 0,

        ]);
        $projects = array(52,61,62,64,67,76,77,83,90);
        $query->andFilterWhere(['=', 'cheque_no', $this->cheque_no]);
        $query->andFilterWhere(['=', 'inst_type', $this->inst_type])
            ->andFilterWhere(['=', 'loans.status', 'collected'])
            ->andFilterWhere(['in','loans.project_id',$projects])
            //->andFilterWhere(['like', 'remarks', $this->remarks])
            //->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            //->andFilterWhere(['like', 'branch_id', $this->branch_id])
            // ->andFilterWhere(['like', 'project', $this->project])
            ->andFilterWhere(['=', 'members.full_name', $this->name])
            ->andFilterWhere(['=', 'members.parentage', $this->parentage])
            ->andFilterWhere(['=', 'grpno', $this->grpno]);

        $query->having('schdl_amnt - credit > 0');


        if ($export) {

            return $query;
        } else {

            return $dataProvider;
        }
    }

}
