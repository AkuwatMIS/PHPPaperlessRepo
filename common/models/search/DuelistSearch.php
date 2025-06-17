<?php

namespace common\models\search;


use common\models\reports\Duelist;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class DuelistSearch extends Duelist
{
    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'loan_amount', 'inst_amnt', 'inst_months', 'disbursement_id', 'branch_id', 'area_id', 'region_id', 'created_by', 'project_id','tranch_no'], 'integer'],
            [['dateapprove', 'recovery', 'chequeno', 'acccode', 'inst_type', 'date_disbursed', 'dateexpiry', 'cheque_date', 'loanexpiry',  'sanction_no', 'expiry_date'], 'safe'],
            [['due', 'overdue', 'balance','tranch_amount'], 'number'],
            [['team_id','name', 'cnic', 'parentage', 'address', 'mobile', 'team_name', 'grpno', 'province_id', 'city_id', 'district_id', 'division_id', 'report_date','branch_ids'], 'safe']
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
        $end = $start = '';
        $member_address='';
        $member_phone='';


        if (isset($params['DuelistSearch']['report_date']) && !empty($params['DuelistSearch']['report_date'])) {
                $date1 = strtotime(date('Y-m-t-18:59', (strtotime($params['DuelistSearch']['report_date'] . '-10'))));

                $myDate = strtotime(date('Y-m-01',strtotime($params['DuelistSearch']['report_date'] . '-10')));
                $date = strtotime(date("Y-m-d-18:59", $myDate) . " -1 months");
                $date = strtotime(date("Y-m-t-18:59", $date));

            $end = strtotime(date('Y-m-t-23:59', (strtotime($params['DuelistSearch']['report_date']))));
            $start = strtotime(date("Y-m-11", strtotime($params['DuelistSearch']['report_date'])) . " -1 month");
            }
            else{
                $end = strtotime(date('Y-m-d-23:59', (strtotime('last day of this month'))));
                $start = strtotime(date('Y-m-11', strtotime('last day of last month')));
            }
        if (isset($params['DuelistSearch']['mobile']) && !empty($params['DuelistSearch']['mobile'])) {
            $member_phone.='and phone="'.$params['DuelistSearch']['mobile'].'"';
        }
        if (isset($params['DuelistSearch']['address']) && !empty($params['DuelistSearch']['address'])) {
            $member_phone.=' and address="'.$params['DuelistSearch']['address'].'"';
        }
        /*print_r(date('Y-m-d',$start).',,,'.date('Y-m-t',$end));
         die();*/
        /*$query = Duelist::find()->select('
                loans.id,loans.application_id,applications.member_id as member_id,teams.name as team_name,loans.sanction_no,loans.inst_amnt,loans.region_id,loans.area_id,loans.branch_id,loans.team_id,loans.field_id,loans.project_id,loans.br_serial,members.full_name  as name,members..cnic as cnic,members.parentage as parentage,
                loan_tranches.date_disbursed ,loans.loan_amount ,loan_tranches.tranch_amount,loan_tranches.tranch_no ,(select address from members_address  where is_current=1 and member_id=members.id '.$member_address.' and address_type="home" limit 1) as address,(select phone from members_phone  where is_current=1 and member_id=members.id and phone_type="Mobile" '.$member_phone.' ORDER by id DESC limit 1) as mobile,
                groups.grp_no as grpno, groups.grp_type as grptype,
                @amountapproved:=(loan_tranches.tranch_amount) ,
                @schdl_till_current_month :=(select coalesce(sum(schedules.schdl_amnt),0) from schedules where( schedules.loan_id = loans.id and schedules.due_date <= "' . ($date1) . '")) as schdl_till_current_month,
                @credit:=(select coalesce(sum(recoveries.amount),0)  from recoveries where( recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <="' . ($date) . '")) as credit,
                ((@schdl_till_current_month - @credit)) as due_amount,
                (@amountapproved-@credit) as outstanding_balance,
        ');*/

        /*$query = Duelist::find()->select('
                loans.id,loans.application_id,applications.member_id as member_id,teams.name as team_name,loans.sanction_no,loans.inst_amnt,loans.region_id,loans.area_id,loans.branch_id,loans.team_id,loans.field_id,loans.project_id,loans.br_serial,members.full_name  as name,members..cnic as cnic,members.parentage as parentage,
                loan_tranches.date_disbursed ,loans.loan_amount ,loan_tranches.tranch_amount,loan_tranches.tranch_no ,(select address from members_address  where is_current=1 and member_id=members.id '.$member_address.' and address_type="home" limit 1) as address,(select phone from members_phone  where is_current=1 and member_id=members.id and phone_type="Mobile" '.$member_phone.' ORDER by id DESC limit 1) as mobile,
                groups.grp_no as grpno, groups.grp_type as grptype,
                @amountapproved:=(loan_tranches.tranch_amount),
                @schdl_till_current_month :=(select (sum(schedules.schdl_amnt)+sum(schedules.charges_schdl_amount)) from schedules where( schedules.loan_id = loans.id and schedules.due_date <= "' . ($date1) . '")) as schdl_till_current_month,
                @credit:=(select coalesce((sum(recoveries.amount)+sum(recoveries.charges_amount)),0)  from recoveries where( recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <="' . ($date) . '")) as credit,
                ((@schdl_till_current_month - @credit)) as due_amount,
                (@amountapproved-@credit) as outstanding_balance,
        '); query changed on 2022-04-04 bcz on -ve due amount*/

        $query = Duelist::find()->select('
                loans.id,loans.application_id,applications.member_id as member_id,teams.name as team_name,loans.sanction_no,loans.inst_amnt,loans.region_id,loans.area_id,loans.branch_id,loans.team_id,loans.field_id,loans.project_id,loans.br_serial,members.full_name  as name,members..cnic as cnic,members.parentage as parentage,
                loan_tranches.date_disbursed ,loans.loan_amount ,loan_tranches.tranch_amount,loan_tranches.tranch_no ,(select address from members_address  where is_current=1 and member_id=members.id '.$member_address.' and address_type="home" limit 1) as address,(select phone from members_phone  where is_current=1 and member_id=members.id and phone_type="Mobile" '.$member_phone.' ORDER by id DESC limit 1) as mobile,
                groups.grp_no as grpno, groups.grp_type as grptype,
                @amountapproved:=(loans.disbursed_amount),  
                @schdl_till_current_month :=(select (sum(schedules.schdl_amnt)+sum(schedules.charges_schdl_amount)) from schedules where( schedules.loan_id = loans.id and schedules.due_date <= "' . ($date1) . '")) as schdl_till_current_month,
                @credit:=(select coalesce((sum(recoveries.amount)),0)  from recoveries where( recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <="' . ($date) . '")) as credit,
                ((@schdl_till_current_month - @credit)) as due_amount,
                (@amountapproved-@credit) as outstanding_balance,
        ');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100 // in case you want a default pagesize
            ]
        ]);
        //$dataProvider->pagination->pageSize =50;

        $query->joinWith('loan');
        $query->joinWith('loan.application.member');
        $query->joinWith('loan.application.group');
        $query->joinWith('loan.application.team');
        //$query->joinWith('application.member.membersAddresses');
        //$query->joinWith('application.member.membersPhones');

        //$query->joinWith('branch');
        //$query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'province_id' => [
                    'asc' => ['province_id' => SORT_ASC],
                    'desc' => ['province_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'grpno' => [
                    'asc' => ['grpno' => SORT_ASC],
                    'desc' => ['grpno' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['cnic' => SORT_ASC],
                    'desc' => ['cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'mobile' => [
                    'asc' => ['mobile' => SORT_ASC],
                    'desc' => ['mobile' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'address' => [
                    'asc' => ['address' => SORT_ASC],
                    'desc' => ['address' => SORT_DESC],
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
                'tranch_amount' => [
                    'asc' => ['tranch_amount' => SORT_ASC],
                    'desc' => ['tranch_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'tranch_no' => [
                    'asc' => ['tranch_no' => SORT_ASC],
                    'desc' => ['tranch_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_tranches.date_disbursed' => [
                    'asc' => ['loan_tranches.date_disbursed' => SORT_ASC],
                    'desc' => ['loan_tranches.datedisburse' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'credit' => [
                    'asc' => ['credit' => SORT_ASC],
                    'desc' => ['credit' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due_amount' => [
                    'asc' => ['due_amount' => SORT_ASC],
                    'desc' => ['due_amount' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'outstanding_balance' => [
                    'asc' => ['outstanding_balance' => SORT_ASC],
                    'desc' => ['outstanding_balance' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due_ampount' => [
                    'asc' => ['due_amount' => SORT_ASC],
                    'desc' => ['due_amount' => SORT_DESC],
                    'default' => SORT_DESC
                ]
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails

            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            //'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'date_disbursed' => $this->date_disbursed,
            'cheque_date' => $this->cheque_date,
            'disbursement_id' => $this->disbursement_id,
            //'loans.region_id' => $this->region_id,
            //'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'loans.team_id' => $this->team_id,
            'team_name' => $this->team_name,
            //'members_phone.phone' => $this->mobile,
            //'members_address.address' => $this->address,
            'loans.project_id' => $this->project_id,
            //'branches.province_id' => $this->province_id,
            //'branches.division_id' => $this->division_id,
            //'branches.district_id' => $this->district_id,
            //'branches.city_id' => $this->city_id,
            'loans.deleted' =>0,

        ]);
        $query->andFilterWhere(['not between', 'loan_tranches.date_disbursed', $start, $end]);
        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['=', 'inst_type', $this->inst_type])
            ->andFilterWhere(['=', 'tranch_no', $this->tranch_no])
            ->andFilterWhere(['=', 'tranch_amount', $this->tranch_amount])
            ->andFilterWhere(['=', 'loans.status', 'collected'])
            ->andFilterWhere(['=', 'loan_tranches.status', 6])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.full_name', $this->name])
            ->andFilterWhere(['like', 'members.cnic', $this->cnic])
            ->andFilterWhere(['like', 'groups.grp_no', $this->grpno]);
        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }
        //  return $dataProvider;
    }

    public function due_list_search($params)
    {
        if (isset($params['DuelistSearch']['report_date'])) {
            if ($params['DuelistSearch']['report_date'] != null) {
                $date_array = explode(' - ', $params['DuelistSearch']['report_date']);
                //$date1=$date_array[0].'-10';
                $date1 = strtotime(date('Y-m-10', strtotime($date_array[0])));
                //$date = strtotime(date('Y-m-t', (strtotime('-1 months',($date1)))));
                $date = strtotime(date("Y-m-t", $date1) . " -1 month");

                /*print_r($date1.' '. $date);
                die();*/
            } else {
                $date1 = strtotime(date('Y-m-10'));
                $date = strtotime(date('Y-m-d'));
            }
        } else {
            $date1 = strtotime(date('Y-m-10'));
            $date = strtotime(date('Y-m-d'));
        }

        $due_date = date('Y-m-d');
        $billing_month = date('Y-m', strtotime($date1));
        $end = strtotime(date('Y-m-t',(strtotime($date1))));
        //$start = strtotime(date('Y-m-11',strtotime('-1 month',($date1))));
        $start = strtotime(date("Y-m-11", $date1) . " -1 month");
        $cur_month=date('Y-m');
        $query = Duelist::find()->select([
            'loans.id','loans.branch_id', 'loans.project_id','loans.application_id', 'loans.sanction_no','loans.inst_amnt','branches.name as branch_name', 'loans.br_serial', 'members.full_name as name', 'members.cnic as cnic', 'members.parentage as parentage',
            '(select address from members_address  where is_current=1  and member_id=members.id and address_type="home"  limit 1) as address', '(select phone from members_phone  where is_current=1  and member_id=members.id and phone_type="Mobile" ORDER by id DESC limit 1) as mobile', 'loan_tranches.date_disbursed', 'coalesce(sum(loan_tranches.tranch_amount),0) as amountapproved',
            'groups.grp_no as grpno', 'groups.grp_type as grptype',
            '@amountapproved:=(loan_tranches.tranch_amount)',

            '@schdl_till_current_month :=(select sum(schedules.schdl_amnt) from schedules where schedules.loan_id = loans.id and schedules.due_date <="' . ($date1) . '") as schdl_till_current_month',
            '@credit:=(select COALESCE(sum(recoveries.amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= "' . ($date1) . '") as credit',
            '(select COALESCE(sum(amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.deleted = 0 and from_unixtime(recoveries.receive_date, \'%Y-%m\') = "' . ($cur_month) . '") as this_month_recovery',
            '((@schdl_till_current_month - @credit)) as due_amount',
            '(@amountapproved-@credit) as outstanding_balance',]);
        $query->joinWith('loan');
        $query->joinWith('loan.application.member');
        $query->joinWith('loan.application.group');
        $query->joinWith('loan.application.member.membersPhones');
        $query->joinWith('loan.branch');
        $query->joinWith('loan.project');
        $this->load($params);

        //return $query->limit(1000)->asArray()->all();
        $query->andFilterWhere([
            'id' => $this->id,
            'application_id' => $this->application_id,
            'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            //'date_disbursed' => $this->date_disbursed,
            'cheque_date' => $this->cheque_date,
            'disbursement_id' => $this->disbursement_id,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'members_phone.phone' => $this->mobile,

        ]);

        if(!empty($this->branch_ids)){
            $query->andFilterWhere(['in', 'loans.branch_id', $this->branch_ids]);
        }
        if(!empty($this->sanction_no)){
            $query->andFilterWhere(['in', 'loans.sanction_no', $this->sanction_no]);
        }
        $query->andFilterWhere(['=', 'loans.status', 'collected']);
        $query->andFilterWhere(['=', 'loan_tranches.status', 6]);
        $query->andFilterWhere(['not between', 'loan_tranches.date_disbursed', $start, $end]);
        /*print_r($query->asArray()->one());
        die('we die here');*/
        $duelist_data = $query->asArray()->all();
//print_r( $query->all());

        $new_duelist_data = array();
        foreach ($duelist_data as $data) {
            $schedule_amount = !empty($data['schdl_till_current_month']) ? $data['schdl_till_current_month'] : 0;
            if($data['credit'] == ''){
                $data['credit'] = 0;
                $data['outstanding_balance'] = $data['amountapproved'];
            }

            $outstanding_balance = $data['outstanding_balance'];
            $amountapproved = $data['amountapproved'];
            if ($data['inst_amnt'] > $outstanding_balance) {
                $data['due_amount'] = $outstanding_balance;
            } else {
                $last_month_schdules = $schedule_amount - $data['inst_amnt'];
                $adv_od = $last_month_schdules - $data['credit'];
                if($adv_od <= 0){
                    $data['due_amount'] = $data['inst_amnt'];
                }else{
                    $data['due_amount'] = $schedule_amount - $data['credit'];
                }
            }

            unset($data['branch']);
            unset($data['project']);
            $new_duelist_data[] = $data;
        }

        return $new_duelist_data;
    }

    public function search_due_vs_recovery($params, $export = false)
    {
        if (isset($params['DuelistSearch']['report_date'])) {
            if ($params['DuelistSearch']['report_date'] != null) {
                $date1 = strtotime($params['DuelistSearch']['report_date'] . '-10');
                //$date = strtotime(date('Y-m-t', (strtotime('-1 months',($date1)))));
                //$date = strtotime(date("Y-m-t", $date1) . " -1 month");
                $date = strtotime(date("Y-m-t", $date1));
                $cur_month=date('Y-m',strtotime($params['DuelistSearch']['report_date']));

            } else {
                $date1 = strtotime(date('Y-m-10'));
                $date =strtotime(date('Y-m-d'));
                $cur_month=date('Y-m');
            }
        } else {
            $date1 =strtotime(date('Y-m-10'));
            $date = strtotime(date('Y-m-d'));
            $cur_month=date('Y-m');
        }
        //'borrowers.team_name as team_name'
        $query = Duelist::find()->select([

            'loans.id', 'loans.application_id','applications.member_id as member_id','teams.name as team_name', 'loans.sanction_no', 'loans.br_serial', 'members.full_name as name', 'members.cnic as cnic', 'members.parentage as parentage',
            '(select address from members_address  where is_current=1  and member_id=members.id  limit 1) as address', '(select phone from members_phone  where is_current=1  and member_id=members.id  limit 1) as mobile', 'loans.date_disbursed', 'loans.loan_amount',
            'groups.grp_no as grpno', 'groups.grp_type as grptype','loan_tranches.tranch_amount','loan_tranches.tranch_no',
            '@amountapproved:=(loan_tranches.tranch_amount)',

            '@schdl_till_current_month :=(select sum(schedules.schdl_amnt) from schedules where schedules.loan_id = loans.id and schedules.due_date <="' . ($date1) . '") as schdl_till_current_month',
            '@credit:=(select COALESCE(sum(recoveries.amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date <= "' . ($date) . '") as credit',
            '@this_month_recovery:=(select COALESCE(sum(amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.deleted=0 and from_unixtime(recoveries.receive_date, \'%Y-%m\') = "' . ($cur_month) . '") as this_month_recovery',
            '((@schdl_till_current_month - @credit)+@this_month_recovery) as due_amount',
            '(@amountapproved-@credit) as outstanding_balance',

        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 100;
        $query->joinWith('loan');
        $query->joinWith('loan.application.member');
        $query->joinWith('loan.application.group');
        $query->joinWith('loan.application.team');
        //$query->joinWith('application.member.membersPhones');
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
                'cnic' => [
                    'asc' => ['cnic' => SORT_ASC],
                    'desc' => ['cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'mobile' => [
                    'asc' => ['mobile' => SORT_ASC],
                    'desc' => ['mobile' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'address' => [
                    'asc' => ['address' => SORT_ASC],
                    'desc' => ['address' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'team_name' => [
                    'asc' => ['team_name' => SORT_ASC],
                    'desc' => ['team_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'this_month_recovery' => [
                    'asc' => ['this_month_recovery' => SORT_ASC],
                    'desc' => ['this_month_recovery' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'tranch_amount' => [
                    'asc' => ['tranch_amount' => SORT_ASC],
                    'desc' => ['tranch_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'tranch_no' => [
                    'asc' => ['tranch_no' => SORT_ASC],
                    'desc' => ['tranch_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'loan_tranches.datedisburse' => [
                    'asc' => ['loan_tranches.datedisburse' => SORT_ASC],
                    'desc' => ['loan_tranches.datedisburse' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'credit' => [
                    'asc' => ['credit' => SORT_ASC],
                    'desc' => ['credit' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due_amount' => [
                    'asc' => ['due_amount' => SORT_ASC],
                    'desc' => ['due_amount' => SORT_DESC],
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
            //'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            //'date_disbursed' => $this->date_disbursed,
            'cheque_date' => $this->cheque_date,
            'disbursement_id' => $this->disbursement_id,

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
            //'members_phone.phone' => $this->mobile,
            'loans.deleted' => 0,

        ]);

        $query->andFilterWhere(['like', 'loan_tranches.cheque_no', $this->cheque_no])
            ->andFilterWhere(['like', 'inst_type', $this->inst_type])
            ->andFilterWhere(['=', 'tranch_no', $this->tranch_no])
            ->andFilterWhere(['=', 'tranch_amount', $this->tranch_amount])
            //->andFilterWhere(['=', 'loans.status', 'collected'])
            ->andFilterWhere(['=', 'loan_tranches.status', 6])
            //->andFilterWhere(['or', ['loans.status'=>'collected'],['and',['loans.status'=>'loan completed'],['>','loan_completed_date',1530316800]]])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'loans.branch_id', $this->branch_id])
            // ->andFilterWhere(['like', 'project', $this->project])
            ->andFilterWhere(['like', 'members.full_name', $this->name])
            ->andFilterWhere(['like', 'members.cnic', $this->cnic])
            ->andFilterWhere(['like', 'groups.grp_no', $this->grpno]);
        if(isset($this->date_disbursed) && !empty($this->date_disbursed)){
            $query->andFilterWhere(['in', 'loans.status', ['loan completed','collected']]);
        }else{
            $query->andFilterWhere(['in', 'loans.status', ['collected']]);
        }
        if (!is_null($this->date_disbursed) && strpos($this->date_disbursed, ' - ') !== false) {
            $date = explode(' - ', $this->date_disbursed);
            $query->andFilterWhere(['between', 'loan_tranches.date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        } else {
            $query->andFilterWhere(['loan_tranches.date_disbursed' => $this->date_disbursed]);
        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }

    }

    public function searchrepeatingborrowers($params, $export = false)
    {
        /*loans.id,loans.borrower_id,loans.sanction_no,loans.region_id,loans.project_id,loans.br_serial,borrowers.name,@cnic:=(borrowers.cnic) as cnic,borrowers.parentage as parentage,
                borrowers.address1 as address, borrowers.mobile as mobile, borrowers.team_name as team_name,loans.datedisburse ,loans.amountapproved,
                groups.grpno as grpno, groups.grptype as grptype,@count:=(select count(*) from borrowers where borrowers.cnic=@cnic) as count*/
        if (isset($params['DuelistSearch']['report_date'])) {
            if ($params['DuelistSearch']['report_date'] != null) {
                $date1 = $params['DuelistSearch']['report_date'] . '-10';
                //$date = strtotime(date('Y-m-t', (strtotime('-1 months', strtotime($date1)))));
                $date = strtotime(date("Y-m-t", strtotime($date1)) . " -1 month");

            } else {
                $date1 =strtotime(date('Y-m-10'));
                $date = strtotime(date('Y-m-d'));
            }
        } else {
            $date1 = strtotime(date('Y-m-10'));
            $date = strtotime(date('Y-m-d'));
        }
        //@count:=(select count(*) from borrowers where borrowers.cnic=@cnic) as count
        //->where(['<','loans.id',378]
        $query = Duelist::find()->select('loans.id,loans.borrower_id,loans.sanction_no,loans.region_id,loans.project_id,loans.br_serial,borrowers.name,
                @cnic:=(borrowers.cnic) as cnic,
                borrowers.parentage as parentage,
                borrowers.address1 as address, borrowers.mobile as mobile, borrowers.team_name as team_name,loans.datedisburse ,loans.amountapproved,
                groups.grpno as grpno, groups.grptype as grptype,@count:=(select count(*) from borrowers where borrowers.cnic=@cnic) as count');
        //->where(['<','loans.id',378]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('borrower');
        $query->joinWith('borrower.group');
        $query->joinWith('branch');
        $query->joinWith('project');
        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'province_id' => [
                    'asc' => ['province_id' => SORT_ASC],
                    'desc' => ['province_id' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'grpno' => [
                    'asc' => ['grpno' => SORT_ASC],
                    'desc' => ['grpno' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnic' => [
                    'asc' => ['cnic' => SORT_ASC],
                    'desc' => ['cnic' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'parentage' => [
                    'asc' => ['parentage' => SORT_ASC],
                    'desc' => ['parentage' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'mobile' => [
                    'asc' => ['mobile' => SORT_ASC],
                    'desc' => ['mobile' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'address' => [
                    'asc' => ['address' => SORT_ASC],
                    'desc' => ['address' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'sanction_no' => [
                    'asc' => ['sanction_no' => SORT_ASC],
                    'desc' => ['sanction_no' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'amountapproved' => [
                    'asc' => ['amountapproved' => SORT_ASC],
                    'desc' => ['amountapproved' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'datedisburse' => [
                    'asc' => ['datedisburse' => SORT_ASC],
                    'desc' => ['datedisburse' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'credit' => [
                    'asc' => ['credit' => SORT_ASC],
                    'desc' => ['credit' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due_amount' => [
                    'asc' => ['due_amount' => SORT_ASC],
                    'desc' => ['due_amount' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'outstanding_balance' => [
                    'asc' => ['outstanding_balance' => SORT_ASC],
                    'desc' => ['outstanding_balance' => SORT_DESC],
                    'default' => SORT_DESC
                ],
                'due_ampount' => [
                    'asc' => ['due_amount' => SORT_ASC],
                    'desc' => ['due_amount' => SORT_DESC],
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
            'borrower_id' => $this->borrower_id,
            'amountapproved' => $this->amountapproved,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            'grace_prd' => $this->grace_prd,
            'disbursement_id' => $this->disbursement_id,
            'loans.branch_id' => $this->branch_id,
            'loans.area_id' => $this->area_id,
            'loans.region_id' => $this->region_id,
            'br_serial' => $this->br_serial,
            'user_id' => $this->user_id,
            'islock' => $this->islock,
            'chqprinted' => $this->chqprinted,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'expiry_date' => $this->expiry_date,
            'project_id' => $this->project_id,
            'dt_entry' => $this->dt_entry,
            'team_name' => $this->team_name,
            'borrowers.name' => $this->name,


            'borrowers.mobile' => $this->mobile,
            'borrowers.address1' => $this->address,
            'loans.project_id' => $this->project_id,
            'branches.province_id' => $this->province_id,
            'branches.division_id' => $this->division_id,
            'branches.district_id' => $this->district_id,
            'branches.city_id' => $this->city_id,


        ]);

        $query->andFilterWhere(['like', 'chequeno', $this->chequeno])
            ->andFilterWhere(['like', 'acccode', $this->acccode])
            ->andFilterWhere(['=', 'inst_type', $this->inst_type])
            ->andFilterWhere(['=', 'dsb_status', 'Collected'])
            ->andFilterWhere(['like', 'funding_line', $this->funding_line])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            //->andFilterWhere(['like', 'branch', $this->branch])
            //->andFilterWhere(['like', 'project', $this->project])
            ->andFilterWhere(['like', 'borrowers.name', $this->name])
            ->andFilterWhere(['like', 'cnic', $this->cnic])
            ->andFilterWhere(['like', 'grpno', $this->grpno]);
        // $query->orderBy('count');
        //$query->having('count>1');
        if ($export) {
            return $query;
        } else {

            return $dataProvider;
        }
        //  return $dataProvider;
    }
}
