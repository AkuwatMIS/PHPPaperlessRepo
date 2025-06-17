<?php

namespace common\models\reports;

use common\models\Loans;
use common\models\LoanTranches;
use Yii;

class Duelist extends LoanTranches
{
    public $name;
    public $cnic;
    public $parentage;
    public $member_id;
    public $address;
    public $mobile;
    public $team_name;
    public $schdl_till_current_month;
    public $credit;
    public $this_month_recovery;
    public $branch_id;
    public $branch_name;
    public $branch_ids;
    public $project;
    public $grpno;
    public $due_date;
    public $due_amount;
    public $outstanding_balance;
    public $province_id;
    public $district_id;
    public $division_id;
    public $city_id;
    public $report_date;
    public $count;
    public $grptype;
    public $team_id;
    public $project_id;
    public $sanction_no;
    public $loan_amount;
    public $application_id;
    public $inst_amnt;
    public $inst_type;
    public $inst_months;
    public $area_id;
    public $region_id;
    public $due;
    public $overdue;
    public $balance;
    public $date_approved;
}