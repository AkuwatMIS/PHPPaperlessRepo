<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\Blacklist;
use common\models\Users;
use common\models\BranchProjectsMapping;
use Yii;

class CalculationHelper
{
    static public function calculatePMT($rate,$nper,$pv,$type=0,$fv=0)
    {
        /*$rate=0.02/12;
        $nper=120;
        $pv= 1000000;*/
        $PMT = (-$fv - $pv * pow(1 + $rate, $nper)) /
            (1 + $rate * $type) /
            ((pow(1 + $rate, $nper) - 1) / $rate);
        return $PMT;
    }

}