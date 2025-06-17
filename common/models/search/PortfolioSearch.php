<?php

namespace common\models\search;


use common\models\reports\Duelist;
use common\models\reports\Portfolio;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class PortfolioSearch extends Portfolio
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'loan_amount', 'inst_amnt', 'inst_months', 'disbursement_id', 'branch_id', 'area_id', 'region_id', 'br_serial', 'created_by', 'is_lock', 'project_id'], 'integer'],
            [['dateapprove', 'recovery', 'cheque_no', 'acccode', 'inst_type', 'date_disbursed', 'dateexpiry', 'cheque_dt', 'dsb_status', 'funding_line', 'loan_expiry', 'remarks', 'old_sanc_no', 'sanction_no', 'expiry_date', 'dt_entry'], 'safe'],
            [['due', 'overdue', 'balance'], 'number'],
            [['region_name', 'area_name', 'branch_name'], 'safe'],
            [['name', 'cnic', 'gender', 'parentage', 'address', 'mobile', 'grpno', 'province_id', 'city_id', 'district_id', 'division_id', 'report_date', 'branch_ids'], 'safe']
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
    public function search_portfolio($params, $export = false)
    {
        ini_set('memory_limit', '16128M'); // or you could use 1G

        $this->load($params);
        $cond = '';
        $rec_cond = '';
        if (isset($this->branch_id) && !empty($this->branch_id)) {
            $cond = " and loans.branch_id=" . $this->branch_id . "";
        }
        if (isset($this->report_date) && !empty($this->report_date)) {
            $date = explode(' - ', $this->report_date);

            $date[1]=$date[1].'-23:59';
            $cond .= " and (t.date_disbursed  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . ")";
            $rec_cond .= " and recoveries.receive_date  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . "";
        }
        if (isset($this->project_id) && !empty($this->project_id)) {
            $cond .= " and loans.project_id=" . $this->project_id . "";
        }
        if (isset($this->sanction_no) && !empty($this->sanction_no)) {
            $cond .= " and loans.sanction_no='" . $this->sanction_no . "'";
        }
        if (isset($this->name) && !empty($this->name)) {
            $cond .= " and members.full_name='" . $this->name . "'";
        }
        if (isset($this->parentage) && !empty($this->parentage)) {
            $cond .= " and members.parentage='" . $this->parentage . "'";
        }

        $connection = Yii::$app->db;
        $query = "SELECT loans.id,loans.area_id,loans.branch_id,loans.application_id,loans.sanction_no,loans.inst_amnt,loans.inst_months,loans.status,
                           projects.name as project_name,branches.name as branch_name,regions.name as region_name,areas.name as area_name,
                           loans.inst_amnt,loans.region_id,loans.project_id,members.full_name AS name,members.dob AS dob, 
                           members.gender AS gender,members.cnic AS cnic, members.parentage AS parentage,t.date_disbursed, 
                           t.cheque_no,inst_months, loans.loan_amount,loans.loan_expiry, t.tranch_amount,t.tranch_no,
                           (select address from members_address where is_current=1 and member_id=members.id  and deleted=0 and address_type=\"home\" limit 1) as address, 
                           (select phone from members_phone where is_current=1 and member_id=members.id  and deleted=0 and phone_type=\"Mobile\" ORDER by id DESC limit 1) as mobile, 
                           groups.grp_no AS grpno,groups.grp_type AS grptype,applications.activity_id,activities.name as activity_name,
                           (select COALESCE (sum(recoveries.amount),0) from recoveries where recoveries.loan_id=loans.id and recoveries.deleted=0 " . $rec_cond . ") as recovery,
                           member_info.cnic_issue_date,member_info.cnic_expiry_date
                            FROM loan_tranches t
                       INNER JOIN loans ON loans.id=t.loan_id
                       INNER JOIN applications ON loans.application_id=applications.id
                       INNER JOIN members ON applications.member_id = members.id
                       INNER JOIN groups ON applications.group_id = groups.id
                       INNER JOIN branches ON branches.id = loans.branch_id
                       INNER JOIN areas ON areas.id = loans.area_id
                       INNER JOIN regions ON regions.id = loans.region_id
                       INNER JOIN projects ON projects.id = loans.project_id
                       LEFT JOIN activities ON activities.id = loans.activity_id
                       LEFT JOIN member_info ON member_info.member_id = members.id
              WHERE 1  " . $cond . "
              AND (loans.deleted=0) 
              AND (t.status > 3) 
              AND (loans.status not in ('not collected','grant'))";
        $portfolio = $connection->createCommand($query)->queryAll();
        return $portfolio;
        if ($export) {
            return $portfolio;
        } else {
            return $dataProvider;
        }

    }

    public function search_portfolio_fund_source($params, $export = false)
    {
        ini_set('memory_limit', '16128M'); // or you could use 1G

        $this->load($params);
        $cond = '';
        $rec_cond = '';
        if (isset($this->branch_id) && !empty($this->branch_id)) {
            $cond = " and loans.branch_id=" . $this->branch_id . "";
        }
        if (isset($this->report_date) && !empty($this->report_date)) {
            $date = explode(' - ', $this->report_date);

            $date[1]=$date[1].'-23:59';
            $cond .= " and (t.date_disbursed  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . ")";
            $rec_cond .= " and recoveries.receive_date  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . "";
        }
        if (isset($this->project_id) && !empty($this->project_id)) {
            $cond .= " and loans.project_id=" . $this->project_id . "";
        }
        if (isset($this->sanction_no) && !empty($this->sanction_no)) {
            $cond .= " and loans.sanction_no='" . $this->sanction_no . "'";
        }
        if (isset($this->name) && !empty($this->name)) {
            $cond .= " and members.full_name='" . $this->name . "'";
        }
        if (isset($this->parentage) && !empty($this->parentage)) {
            $cond .= " and members.parentage='" . $this->parentage . "'";
        }

        $connection = Yii::$app->db;
        $query = "SELECT loans.id,loans.area_id,loans.branch_id,loans.application_id,loans.sanction_no,loans.inst_amnt,loans.inst_months,loans.status,
                           projects.name as project_name,branches.name as branch_name,regions.name as region_name,areas.name as area_name,
                           loans.inst_amnt,loans.region_id,loans.project_id,members.full_name AS name,members.dob AS dob, 
                           members.gender AS gender,members.cnic AS cnic, members.parentage AS parentage,t.date_disbursed, 
                           t.cheque_no,inst_months, loans.loan_amount,loans.loan_expiry, t.tranch_amount,t.tranch_no,
                           (select address from members_address where is_current=1 and member_id=members.id  and deleted=0 and address_type=\"home\" limit 1) as address, 
                           (select phone from members_phone where is_current=1 and member_id=members.id  and deleted=0 and phone_type=\"Mobile\" ORDER by id DESC limit 1) as mobile, 
                           groups.grp_no AS grpno,groups.grp_type AS grptype,applications.activity_id,activities.name as activity_name,
                           (select COALESCE (sum(recoveries.amount),0) from recoveries where recoveries.loan_id=loans.id and recoveries.deleted=0 " . $rec_cond . ") as recovery,
                            IF(ISNULL(funds.name)=1, 'N/A', funds.name) AS funding_source 
                            FROM loan_tranches t
                       INNER JOIN loans ON loans.id=t.loan_id
                       INNER JOIN applications ON loans.application_id=applications.id
                       INNER JOIN members ON applications.member_id = members.id
                       INNER JOIN groups ON applications.group_id = groups.id
                       INNER JOIN branches ON branches.id = loans.branch_id
                       INNER JOIN areas ON areas.id = loans.area_id
                       INNER JOIN regions ON regions.id = loans.region_id
                       INNER JOIN projects ON projects.id = loans.project_id
                       LEFT JOIN activities ON activities.id = loans.activity_id
                       LEFT JOIN project_fund_detail ON project_fund_detail.id = t.batch_id 
                       LEFT JOIN funds ON funds.id = project_fund_detail.fund_id
              WHERE 1  " . $cond . "
              AND (loans.deleted=0) 
              AND (t.status > 3) 
              AND (loans.status != 'not collected')";
        $portfolio = $connection->createCommand($query)->queryAll();
        return $portfolio;
        if ($export) {
            return $portfolio;
        } else {
            return $dataProvider;
        }

    }

    public function search_portfolio_lwc($params, $export = false)
    {
        ini_set('memory_limit', '16128M'); // or you could use 1G
        $query = Portfolio::find()->select('
                regions.name as region_name, areas.name as area_name, branches.name as branch_name,loans.id,loans.area_id,loans.branch_id,loans.application_id,loans.sanction_no,loans.inst_amnt,loans.region_id,loans.project_id,members.full_name  as name,members.dob as dob,members.gender as gender,members.marital_status as marital_status,members.cnic as cnic,members.parentage as parentage,
                loans.date_disbursed,loans.inst_type,loans.cheque_no ,inst_months,loans.loan_amount,loans.loan_expiry,loans.loan_amount,
                groups.grp_no as grpno, groups.grp_type as grptype,applications.activity_id,
        ');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = 50;
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        $query->joinWith('application.member.membersAddresses');
        $query->joinWith('application.member.membersPhones');

        $query->joinWith('region');
        $query->joinWith('area');
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
                'loan_amount' => [
                    'asc' => ['loan_amount' => SORT_ASC],
                    'desc' => ['loan_amount' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'date_disbursed' => [
                    'asc' => ['date_disbursed' => SORT_ASC],
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
            'application_id' => $this->application_id,
            'loans.project_id' => $this->project_id,
            'date_approved' => $this->date_approved,
            'loan_amount' => $this->loan_amount,
            'inst_amnt' => $this->inst_amnt,
            'inst_months' => $this->inst_months,
            //'date_disbursed' => $this->date_disbursed,
            'cheque_dt' => $this->cheque_dt,
            'disbursement_id' => $this->disbursement_id,
            'activity_id' => $this->activity_id,
            'product_id' => $this->product_id,
            'group_id' => $this->group_id,
            'loans.region_id' => $this->region_id,
            'loans.area_id' => $this->area_id,
            'loans.branch_id' => $this->branch_id,
            'team_id' => $this->team_id,
            'field_id' => $this->field_id,
            'loan_expiry' => $this->loan_expiry,
            'loan_completed_date' => $this->loan_completed_date,
            'br_serial' => $this->br_serial,
            'due' => $this->due,
            'overdue' => $this->overdue,
            'balance' => $this->balance,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'team_name' => $this->team_name,
            'members_phone.phone' => $this->mobile,
            'members_address.address' => $this->address,
            'branches.province_id' => $this->province_id,
            'branches.division_id' => $this->division_id,
            'branches.district_id' => $this->district_id,
            'branches.city_id' => $this->city_id,


        ]);
        $query->andFilterWhere(['like', 'cheque_no', $this->cheque_no])
            ->andFilterWhere(['=', 'inst_type', $this->inst_type])
            ->andFilterWhere(['!=', 'loans.status', 'Not Collected'])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'old_sanc_no', $this->old_sanc_no])
            ->andFilterWhere(['like', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.full_name', $this->name])
            ->andFilterWhere(['like', 'members.cnic', $this->cnic])
            ->andFilterWhere(['like', 'members.parentage', $this->parentage])
            ->andFilterWhere(['like', 'groups.grp_no', $this->grpno]);


        if (!is_null($this->report_date) && strpos($this->report_date, ' - ') !== false) {
            $date = explode(' - ', $this->report_date);
            $query->andFilterWhere(['between', 'loans.date_disbursed', strtotime($date[0]), strtotime($date[1])]);
        }

        if ($export) {
            return $query;
        } else {
            return $dataProvider;
        }

    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function portfolio_psic($params, $d)
    {
        /*print_r($d[0]);
        die();*/
        $start_date = strtotime(date('Y-m-d 01:01:01', strtotime($d[0])));
        $end_date = strtotime(date('Y-m-d 23:59:59', strtotime($d[1])));
        $query = Portfolio::find()->select([
            //'regions.name as region_name', 'areas.name as area_name', 'branches.name as branch_name',
            'branches.code as branch_code',
            'loans.sanction_no', 'members.full_name as name', 'members.parentage as parentage', 'members.cnic as cnic', 'members.dob',
            'loans.loan_amount', 'members.gender', 'groups.grp_no as grpno','(loans.date_disbursed-members.dob)/31536000 as age' ,
            'activities.name as purpose', 'products.name as product', 'loans.date_disbursed', 'loans.loan_expiry', 'loans.cheque_no',
            //'(select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "'.$d[1].'") as recovery',
            //'IF (((loans.status = "loan completed") and loans.loan_completed_date <= "' . $end_date . '"), (loans.loan_amount), (select COALESCE(sum(recoveries.amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.receive_date <= "' . $end_date . '")) as recovery',
            //'(select COALESCE(sum(recoveries.amount),0) from recoveries where recoveries.loan_id = loans.id and recoveries.receive_date <= "' . $end_date . '") as recovery',
            'projects.name as project',
            '(select address from members_address where is_current=1 and member_id=members.id and address_type="home" limit 1) as address',
            '(select phone from members_phone where is_current=1 and member_id=members.id and phone_type="Mobile" limit 1) as mobile']);

        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('branch');
        $query->joinWith('region');
        $query->joinWith('area');
        $query->joinWith('project');
        $query->joinWith('application.activity');
        $query->joinWith('application.product');
        $query->joinWith('group');

        $this->load($params);

        $query->filterWhere(['between', 'loans.date_disbursed', $start_date, $end_date])
            ->andFilterWhere(['=', 'loans.project_id', 1])
            //->andFilterWhere(['=', 'applications.product_id', 5])
            ->andFilterWhere(['!=', 'loans.status', 'not collected']);
        //->andFilterWhere(['=', 'loans.project_id', '4']);
        /*if (!is_null($this->datedisburse) && strpos($this->datedisburse, ' - ') !== false ) {
            $date = explode(' - ', $this->datedisburse);
            $query->andFilterWhere(['between', 'datedisburse', $date[0], $date[1]]);
        }else{
            $query->andFilterWhere(['datedisburse'=> $this->datedisburse]);
        }*/

        return $query->all();
        //  return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function portfolio_report($params, $d)
    {
        /*print_r($d[0]);
        die();*/
        $query = Portfolio::find()->select(['branches.code as branch_code', 'loans.sanction_no', 'districts.name as district',
            'borrowers.name as name', 'borrowers.parentage as parentage', 'borrowers.cnic as cnic', 'borrowers.dob',
            '@amountapproved:=(loans.amountapproved) as amountapproved', 'borrowers.gender',
            'activities.name as purpose', 'loans.datedisburse', 'loans.inst_amnt', 'loans.loanexpiry',
            '(select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "' . $d[1] . '") as recovery',
            'projects.name as project', 'borrowers.mobile as mobile', 'borrowers.address1 as address']);

        $query->joinWith('borrower');
        $query->joinWith('branch');
        $query->joinWith('branch.district');
        $query->joinWith('project');
        $query->joinWith('borrower.activity');

        $this->load($params);

        $query->filterWhere(['between', 'loans.datedisburse', $d[0], $d[1]])
            //->andFilterWhere(['=', 'loans.region_id', '2'])
            //->andFilterWhere(['=', 'branches.district_id', '9'])
            /*->andFilterWhere(['=', 'loans.dsb_status', 'Collected']);*/
            ->andFilterWhere(['!=', 'loans.dsb_status', 'Not Collected'])
            ->andFilterWhere(['=', 'loans.project_id', '36']);
        /*if (!is_null($this->datedisburse) && strpos($this->datedisburse, ' - ') !== false ) {
            $date = explode(' - ', $this->datedisburse);
            $query->andFilterWhere(['between', 'datedisburse', $date[0], $date[1]]);
        }else{
            $query->andFilterWhere(['datedisburse'=> $this->datedisburse]);
        }*/
        /*print_r($query->one());
        die();*/
        return $query->all();
        //  return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function portfolio_disabled($params)
    {
        /*print_r($d[0]);
        die();*/
        $query = Portfolio::find()->select(['branches.code as branch_code', 'loans.sanction_no',
            'borrowers.name as name', 'borrowers.parentage as parentage', 'borrowers.cnic as cnic', 'borrowers.dob', 'borrowers.education as education', 'districts.name as district',
            '@amountapproved:=(loans.amountapproved) as amountapproved', 'borrowers.gender',
            'activities.name as purpose', 'loans.datedisburse', 'loans.inst_amnt', 'loans.loanexpiry',
            //'IF ((loans.dsb_status = "Completed"), (loans.amountapproved), (select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2018-03-31")) as recovery',
            '(select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2019-01-31") as recovery',
            'projects.name as project', 'borrowers.mobile as mobile', 'borrowers.address1 as address',
            'borrowers.khidmat_card_holder', 'borrowers.disability', 'borrowers.nature', 'borrowers.physical_disability',
            'borrowers.visual_disability', 'borrowers.disabilities_instruments', 'borrowers.communicative_disability']);

        $query->joinWith('borrower');
        $query->joinWith('branch');
        $query->joinWith('branch.district');
        $query->joinWith('project');
        $query->joinWith('borrower.activity');

        $this->load($params);

        $query->filterWhere(['between', 'loans.datedisburse', '2019-01-01', '2019-01-31'])
            //->andFilterWhere(['=', 'loans.region_id', '2'])
            ->andFilterWhere(['!=', 'loans.dsb_status', 'Not Collected'])
            ->andFilterWhere(['=', 'loans.project_id', '27']);
        /*if (!is_null($this->datedisburse) && strpos($this->datedisburse, ' - ') !== false ) {
            $date = explode(' - ', $this->datedisburse);
            $query->andFilterWhere(['between', 'datedisburse', $date[0], $date[1]]);
        }else{
            $query->andFilterWhere(['datedisburse'=> $this->datedisburse]);
        }*/
        /*print_r($query->one());
        die();*/
        return $query->all();
        //  return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function portfolio_tevta($params, $d)
    {
        /*print_r($d[0]);
        die();*/
        $query = Portfolio::find()->select(['branches.code as branch_code', 'loans.sanction_no',
            'borrowers.name as name', 'borrowers.parentage as parentage', 'borrowers.cnic as cnic', 'borrowers.dob', 'borrowers.education as education', 'districts.name as district',
            '@amountapproved:=(loans.amountapproved) as amountapproved', 'borrowers.gender',
            'activities.name as purpose', 'loans.datedisburse', 'loans.inst_amnt', 'loans.loanexpiry',
            //'IF ((loans.dsb_status = "Completed"), (loans.amountapproved), (select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2018-03-31")) as recovery',
            '(select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2019-01-31") as recovery',
            'projects.name as project', 'borrowers.mobile as mobile', 'borrowers.address1 as address',
            'borrowers.institute_name', 'borrowers.type_of_diploma', 'borrowers.duration_of_diploma',
            'borrowers.year', 'borrowers.Pbte_or_Ttb', 'borrowers.registration_no', 'borrowers.roll_no']);

        $query->joinWith('borrower');
        $query->joinWith('branch');
        $query->joinWith('branch.district');
        $query->joinWith('project');
        $query->joinWith('borrower.activity');

        $this->load($params);

        $query->filterWhere(['between', 'loans.datedisburse', $d[0], $d[1]])
            //->andFilterWhere(['=', 'loans.region_id', '2'])
            ->andFilterWhere(['!=', 'loans.dsb_status', 'Not Collected'])
            ->andFilterWhere(['=', 'loans.project_id', '17']);
        /*if (!is_null($this->datedisburse) && strpos($this->datedisburse, ' - ') !== false ) {
            $date = explode(' - ', $this->datedisburse);
            $query->andFilterWhere(['between', 'datedisburse', $date[0], $date[1]]);
        }else{
            $query->andFilterWhere(['datedisburse'=> $this->datedisburse]);
        }*/
        /*print_r($query->one());
        die();*/
        return $query->all();
        //  return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function portfolio_kissan($params, $d)
    {
        /*print_r($d[0]);
        die();*/
        $query = Portfolio::find()->select(['branches.code as branch_code', 'loans.sanction_no',
            'borrowers.name as name', 'borrowers.parentage as parentage', 'borrowers.cnic as cnic', 'borrowers.dob', 'borrowers.education as education', 'districts.name as district',
            '@amountapproved:=(loans.amountapproved) as amountapproved', 'borrowers.gender',
            'activities.name as purpose', 'loans.datedisburse', 'loans.inst_amnt', 'loans.loanexpiry',
            //'IF ((loans.dsb_status = "Completed"), (loans.amountapproved), (select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2018-03-31")) as recovery',
            '(select COALESCE(sum(recoveries.credit),0) from recoveries where recoveries.loan_id = loans.id and recoveries.recv_date <= "2018-12-27") as recovery',
            'projects.name as project', 'borrowers.mobile as mobile', 'borrowers.address1 as address',
            'borrowers.owner', 'borrowers.landAreaSize', 'borrowers.landAreaType',
            'borrowers.villageName', 'borrowers.ucNumber', 'borrowers.ucName', 'borrowers.cropType', 'borrowers.crops']);

        $query->joinWith('borrower');
        $query->joinWith('branch');
        $query->joinWith('branch.district');
        $query->joinWith('project');
        $query->joinWith('borrower.activity');

        $this->load($params);

        $query->filterWhere(['between', 'loans.datedisburse', $d[0], $d[1]])
            //->andFilterWhere(['=', 'loans.region_id', '2'])
            ->andFilterWhere(['!=', 'loans.dsb_status', 'Not Collected'])
            ->andFilterWhere(['=', 'loans.project_id', '3']);
        /*if (!is_null($this->datedisburse) && strpos($this->datedisburse, ' - ') !== false ) {
            $date = explode(' - ', $this->datedisburse);
            $query->andFilterWhere(['between', 'datedisburse', $date[0], $date[1]]);
        }else{
            $query->andFilterWhere(['datedisburse'=> $this->datedisburse]);
        }*/
        /*print_r($query->one());
        die();*/
        return $query->all();
        //  return $dataProvider;
    }
}
