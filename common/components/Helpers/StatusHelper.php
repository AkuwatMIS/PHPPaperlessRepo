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

class StatusHelper
{
    static public function projectFundDetailStatus($status)
    {
        if($status == 0) {
            $status_string = 'Pending';
        } elseif ($status == 1) {
            $status_string = 'Approved';
        }elseif ($status == 2){
            $status_string = 'Fund Received';
        }else{
            $status_string = 'Rejected';
        }
        return $status_string;
    }

}