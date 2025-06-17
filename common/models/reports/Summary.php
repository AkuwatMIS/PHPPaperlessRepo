<?php

namespace common\models\reports;

use common\models\Branches;
use common\models\Loans;
use Yii;

class Summary extends Branches
{

    public $disbursement;
    public $due;
    public $overdue;
    public $recovery;
    public $date;
    public $schedule;
    public $province_id;
    public $district_id;
    public $division_id;
    public $city_id;
    public $cih;


}