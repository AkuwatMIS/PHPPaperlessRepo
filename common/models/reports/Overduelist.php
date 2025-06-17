<?php

namespace common\models\reports;

use common\models\Loans;
use Yii;

class Overduelist extends Loans
{
    public $name;
    public $parentage;
    public $overdue_amount;
    public $schdl_amnt;
    public $credit;
    public $grpno;
    public $outstanding_balance;
    public $province_id;
    public $district_id;
    public $division_id;
    public $city_id;
    public $report_date;

}