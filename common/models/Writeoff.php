<?php

namespace common\models;

use common\models\Applications;
use common\models\Loans;
use common\models\LoanTranches;
use Yii;

class Writeoff extends Loans
{
    public $member_name;
    public $region_name;
    public $project_name;
    public $area_name;
    public $branch_name;
    public $mobile;
    public $write_off_amount;
    public $write_off_date;
    public $write_off_by;
    public $member_parentage;
    public $member_cnic;
    public $activity_name;
}