<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\Branches;
use common\models\ConnectionBanks;
use Yii;

class ArchiveReportsHelper{
    public static function branch_ids($branch_codes)
    {
        $arr_ids = [];
        $arr_codes = explode(",", $branch_codes);

        foreach ($arr_codes as $mem) {
            $mem = Branches::find()->where(['code' => $mem])->one()->id;
            array_push($arr_ids, $mem);
        }
        return $arr_ids;
    }
    static public function getBanksResources(){
        return ConnectionBanks::find()->all();
    }
}