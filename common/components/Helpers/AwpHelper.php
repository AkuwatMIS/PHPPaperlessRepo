<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 14/09/17
 * Time: 11:19 AM
 */

namespace common\components\Helpers;

use common\models\Areas;
use common\models\Branches;
use common\models\BranchProjects;
use common\models\BranchProjectsMapping;
//use common\models\CihTransactionsMapping;
use common\models\ProgressReports;
use common\models\Projects;
use common\models\Regions;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Users;
use common\components\NumberHelper;

class AwpHelper
{
    public static function getBranchidfromcode($branch_id)
    {
        return Branches::find()->select('id')->where(['id' => $branch_id])->asArray()->one();

    }
    public static function getClosedBranches()
    {
        return [
            '1704',
            '1411',
            '3018',
            '2311',
            '3216',
            '2103',
            '6407',
            '1742',
            '2115',
            '1756',
            '34402',
            '2804',
            '102',
            '2908',
            '3601',
            '805',
            '806',
            '1706',
            '1724',
            '402',
            '403',
            '2205',
            '2201',
            '14302',
            '607',
            '606',
            '601',
            '3204',
            '3007',
            '1728',
            '1730',
            '1732',
            '902',
            '3502',
            '3503',
            '2913',
            '3009',
            '3010',
            '2604',
            '501',
            '503',
            '504',
            '109',
            '208',
            '818',
            '2405',
            '1202',
            '1904',
            '2808',
            '3211',
            '1701',
            '311',
            '209',
            '2211',
            '2212',
            '2218',
            '711',
            '1407',
            '1111',
            '2221',
            '3303',
            '3215',
            '3217',
            '820',
            '1814',
            '1815',
            '1816',
            '1817',
            '1737',
            '1739',
            '1741',
            '1819',
            '822',
            '1608',
            '1905',
            '1906',
            '2114',
            '2814',
            '12502',
            '1006',
            '829',
            '1413',
            '1414',
            '1115',
            '1303',
            '1304',
            '830',
            '831',
            '832',
            '833',
            '6408',
            '6409',
            '2503',
            '2606',
            '2607',
            '2608',
            '2609',
            '2610',
            '2611',
            '3220',
            '3221',
            '1750',
            '1751',
            '1909',
            '1910',
            '1911',
            '2406',
            '312',
            '2308',
            '2309',
            '2310',
            '407',
            '2916',
            '3406',
            '1822',
            '1823',
            '1824',
            '1825',
            '1826',
            '1828',
            '508',
            '509',
            '3307',
            '3308',
            '114',
            '2818',
            '2820',
            '2821',
            '2823',
            '214',
            '215',
            '216',
            '2919',
            '834',
            '835',
            '2312',
            '3407',
            '3409',
            '612',
            '613',
            '313',
            '3223',
            '3125',
            '3126',
            '3127',
            '3133',
            '2825',
            '2826',
            '3513',
            '3515',
            '6412',
            '1506',
            '1507',
            '1508',
            '1611',
            '1117',
            '1118',
            '1119',
            '1120',
            '3224',
            '1753',
            '1754',
            '1755',
            '2116',
            '1213',
            '1214',
            '3135',
            '1416',
            '2225',
            '2706',
            '836',
            '837',
            '1305',
            '1913',
            '6413',
            '1417',
            '838',
            '2829',
            '2831',
            '2832',
            '2835',
            '2836',
            '2933',
            '839',
            '1124',
            '3013',
            '3015',
            '3017',
            '1757',
            '3517',
            '1509',
            '3123',
            '3138',
            '3143',
            '3227',
            '2117',
            '1613',
            '1009',
            '1011',
            '2504',
            '2612',
            '116',
            '1418',
            '1831',
            '1832',
            '1759',
            '1215',
            '6414',
            '8307',
            '45104',
            '3142',
            '3144',
            '3014',
            '1758',
            '1808',
            '17206',
            '12209',
            '11505',
            '14102',
            '14105',
            '14308',
            '14310',
            '13206',
            '14207',
            '14208',
            '11601',
            '13105',
            '13106',
            '13107',
            '11703',
            '8309',
            '14209',
            '14210',
            '14211',
            '14212',
            '14107',
            '11405',
            '17301',
            '1821',
            '1827',
            '14106',
            '14311'
        ];

    }

    public static function getClosedOlpBranches()
    {
        return [
            '371',
            '727',
            '725',
            '2',
            '271',
            '375',
            '647',
            '646',
            '658',
            '766',
            '496',
            '314',
            '534',
            '527',
            '783',
            '483',
            '656',
            '537',
            '775',
            '239',
            '649',
            '750',
            '652',
            '730',
            '389',
            '89',
            '107',
            '111',
            '113',
            '115',
            '391',
            '393',
            '395',
            '461',
            '525',
            '519',
            '523',
            '760',
            '759',
            '758',
            '774',
            '308',
            '396',
            '398',
            '399',
            '401',
            '298',
            '514',
            '127',
            '472',
            '522',
            '520',
            '734',
            '539',
            '503',
            '716',
            '719',
            '318',
            '589',
            '584',
            '585',
            '587',
            '729',
            '740',
            '744',
            '682',
            '757',
            '181',
            '183',
            '767',
            '765',
            '762',
            '481',
            '553',
            '501',
            '746',
            '742',
            '738',
            '702',
            '340',
            '426',
            '427',
            '428',
            '642',
            '430',
            '640',
            '795',
            '213',
            '756',
            '214',
            '17',
            '18',
            '824',
            '28',
            '30',
            '31',
            '509',
            '513',
            '343',
            '26',
            '27',
            '690',
            '691',
            '347',
            '621',
            '360',
            '48',
            '362',
            '364',
            '810',
            '605',
            '761',
            '61'
        ];

    }

    public static function getAgriBranches()
    {
        return [304,
            311,
            312,
            404,
            407,
            503,
            507,
            508,
            509,
            510,
            511,
            710,
            818,
            829,
            830,
            831,
            832,
            833,
            836,
            837,
            1115,
            1116,
            1303,
            1304,
            1407,
            1413,
            1414,
            1416,
            1417,
            1750,
            1751,
            1821,
            1822,
            1823,
            1824,
            1825,
            1826,
            1827,
            1828,
            1831,
            1832,
            1905,
            1906,
            1907,
            1909,
            1910,
            2303,
            2308,
            2309,
            2310,
            2312,
            2405,
            2406,
            2503,
            2606,
            2607,
            2608,
            2609,
            2610,
            2611,
            2814,
            2916,
            3010,
            3202,
            3218,
            3219,
            3220,
            3221,
            3222,
            3304,
            3307,
            3308,
            3309,
            3405,
            3406,
            3409,
            3507,
            3513,
            6408,
            6409,
            6410,
            6413,
            6414,
        ];

    }

    public static function getBranchprojects($branch)
    {
        return BranchProjects::find()->select('project_id')->where(['branch_id' => $branch])->asArray()->all();
    }

    public static function getProjectname($branch)
    {
        return Projects::find(/*['name']*/)->select('funding_line as id,name')->where(['id' => $branch])->asArray()->all();

    }

    public static function getProject($project_id)
    {
        return Projects::find(/*['name']*/)->select('name')->where(['id' => $project_id])->asArray()->one();

    }

    public static function getProjectcode($project_id)
    {
        return Projects::find(/*['name']*/)->select('code')->where(['id' => $project_id])->asArray()->one();

    }

    public static function getTotal($provider, $columnName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$columnName];
        }
        return number_format($total);
    }
    public static function getTotalthree($provider, $column1,$column2,$column3)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += ($item[$column1]-$item[$column2])+$item[$column3];
        }
        return number_format($total);
    }
    public static function getTotalTwoColumns($provider, $columnName1,$columnName2)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$columnName1]-$item[$columnName2];
        }
        return number_format($total);
    }
    /*public static function getCih($branch_id,$cih,$partial_cih,$type)
     {
         $partial_sum=\cih\models\CihTransactionsMapping::find()->where(['branch_id'=>(int)$branch_id,'status'=>0,'type'=>$type])->sum('amount');
         $f_cih = ($cih + ($partial_cih - $partial_sum));
         return $f_cih;
     }*/
    public static function getDivideTwoColumns($provider, $columnName1,$columnName2)
    {
        $total1= 0;
        $total2 = 0;
        foreach ($provider as $item) {
            $total1 += $item[$columnName1];
            $total2 += $item[$columnName2];
        }
        if($total2!=0) {
            $final = $total1 / $total2;
            return number_format($final);
        }
        else{
            return 0;
        }
    }
    public static function getMonths()
    {

        return array(
            "" => "",

            "2018-07" => "July-2018",
            "2018-08" => "August-2018",
            "2018-09" => "September-2018",
            "2018-10" => "October-2018",
            "2018-11" => "November-2018",
            "2018-12" => "December-2018",
            "2019-01" => "January-2019",
            "2019-02" => "February-2019",
            "2019-03" => "March-2019",
            "2019-04" => "April-2019",
            "2019-05" => "May-2019",
            "2019-06" => "June-2019",

        );
    }
    public static function getMonthName()
    {

        return array(
            "" => "",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December",
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
        );
    }
    public static function getAwpMonths()
    {

        return array(
            "" => "Select Month",
            "2018-07" => "July-2018",
            "2018-08" => "August-2018",
            "2018-09" => "September-2018",
            "2018-10" => "October-2018",
            "2018-11" => "November-2018",
            "2018-12" => "December-2018",
            "2019-01" => "January-2019",
            "2019-02" => "February-2019",
            "2019-03" => "March-2019",
            "2019-04" => "April-2019",
            "2019-05" => "May-2019",
            "2019-06" => "June-2019",

        );
    }
    public static function getAwpMonthUpdated()
    {

        return array(
            "" => "Select Month",
//            "2018-07" => "July-2018",
//            "2018-08" => "August-2018",
//            "2018-09" => "September-2018",
//            "2018-10" => "October-2018",
//            "2018-11" => "November-2018",
//            "2018-12" => "December-2018",
//            "2019-01" => "January-2019",
//            "2019-02" => "February-2019",
//            "2019-03" => "March-2019",
//            "2019-04" => "April-2019",
//            "2019-05" => "May-2019",
//            "2019-06" => "June-2019",
//
//            "2019-07" => "July-2019",
//            "2019-08" => "August-2019",
//            "2019-09" => "September-2019",
//            "2019-10" => "October-2019",
//            "2019-11" => "November-2019",
//            "2019-12" => "December-2019",
//            "2020-01" => "January-2020",
//            "2020-02" => "February-2020",
//            "2020-03" => "March-2020",
//            "2020-04" => "April-2020",
//            "2020-05" => "May-2020",
//            "2020-06" => "June-2020",
//            "2020-07" => "July-2020",
//            "2020-08" => "August-2020",
//            "2020-09" => "September-2020",
//            "2020-10" => "October-2020",
//            "2020-11" => "November-2020",
//            "2020-12" => "December-2020",
//
//            "2021-01" => "January-2021",
//            "2021-02" => "February-2021",
//            "2021-03" => "March-2021",
//            "2021-04" => "April-2021",
//            "2021-05" => "May-2021",
//            "2021-06" => "June-2021",
//
//            "2021-07" => "July-2021",
//            "2021-08" => "August-2021",
//            "2021-09" => "September-2021",
//            "2021-10" => "October-2021",
//            "2021-11" => "November-2021",
//            "2021-12" => "December-2021",
//
//            "2022-01" => "January-2022",
//            "2022-02" => "February-2022",
//            "2022-03" => "March-2022",
//            "2022-04" => "April-2022",
//            "2022-05" => "May-2022",
//            "2022-06" => "June-2022",
//            "2022-07" => "July-2022",
//            "2022-08" => "August-2022",
//            "2022-09" => "September-2022",
//            "2022-10" => "October-2022",
//            "2022-11" => "November-2022",
//            "2022-12" => "December-2022",
//
//            "2023-01" => "January-2023",
//            "2023-02" => "February-2023",
//            "2023-03" => "March-2023",
//            "2023-04" => "April-2023",
//            "2023-05" => "May-2023",
//            "2023-06" => "June-2023",


            "2023-07" => "July-2023",
            "2023-08" => "August-2023",
            "2023-09" => "September-2023",
            "2023-10" => "October-2023",
            "2023-11" => "November-2023",
            "2023-12" => "December-2023",

            "2024-01" => "January-2024",
            "2024-02" => "February-2024",
            "2024-03" => "March-2024",
            "2024-04" => "April-2024",
            "2024-05" => "May-2024",
            "2024-06" => "June-2024",

            "2024-07" => "July-2024",
            "2024-08" => "August-2024",
            "2024-09" => "September-2024",
            "2024-10" => "October-2024",
            "2024-11" => "November-2024",
            "2024-12" => "December-2024",

            "2025-01" => "January-2025",
            "2025-02" => "February-2025",
            "2025-03" => "March-2025",
            "2025-04" => "April-2025",
            "2025-05" => "May-2025",
            "2025-06" => "June-2025",


        );
    }
    public static function getAreaprojects($area)
    {
        $array = array();
        $branches = Branches::find()->select(['id'])->where(['area_id' => $area])->asArray()->all();

        foreach ($branches as $branch) {

            array_push($array, $branch['id']);
        }
        return BranchProjectsMapping::find()->select('project_id')->where(['in', 'branch_id', $array])->distinct()->asArray()->all();


    }
    public static function parse_json_awp($progress){
        /*echo'<pre>';
        print_r($progress);
        die('hi');*/
        $big_array  = [];
        if(empty($progress)){
            return json_encode($big_array);
        }
        $result = array();
        $i = 0;
        /*echo '<pre>';
        print_r($progress);
        die();*/
        foreach($progress as $p){
            unset($p['region']);
            unset($p['area']);
            unset($p['branch']);
            $result[$i]['pr']['project_id'] = $p['project_id'];
            //unset($p['progress']);
            $result[$i]['pd'] = $p;
            $i++;
        }

        $branches = ArrayHelper::map(StructureHelper::getBranches(), 'id', 'name');
        $areas = ArrayHelper::map(StructureHelper::getAreas(), 'id', 'name');
        $regions = ArrayHelper::map(StructureHelper::getRegions(), 'id', 'name');
        $temp           = [];
        $old_region_id  = 0;
        $old_area_id    = 0;
        $old_branch_id  = 0;
        end($result);
        /*echo '<pre>';
        print_r($result);
        die();*/
        $last_key = key($result);
        foreach ($result as $key => $one){
            $pd = $one['pd'];
            if($old_area_id==0){
                $old_region_id = $pd['region_id'];
                $old_area_id = $pd['area_id'];
                $old_branch_id = $pd['branch_id'];
            }
            if($pd['area_id']==$old_area_id){
                $temp[] = $pd;
                if($last_key == $key){
                    $big_array[$old_region_id][$old_area_id] = $temp;
                }
                continue;
            }else{
                if($last_key == $key){
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    unset($temp);
                    $temp[] = $pd;
                    $big_array[$pd['region_id']][$pd['area_id']] = $temp;

                }else{
                    $big_array[$old_region_id][$old_area_id] = $temp;
                    $old_region_id  = $pd['region_id'];
                    $old_area_id    = $pd['area_id'];
                    $old_branch_id  = $pd['branch_id'];

                    unset($temp);
                    $temp[] = $pd;
                }

            }
        }

//        echo'<pre>';
//        print_r($big_array);
//        die('hi');
        $temp_sum       = array('id'=>0,'no_of_loans'=>0,'amount_disbursed'=>0,'monthly_recovery'=>0,'funds_required'=>0,'actual_recovery'=>0,'actual_disbursement'=>0);
        $new_big_array  = array();
        $grand_sum      = $temp_sum;
        $grand_sum['id'] = 100;
        $grand_sum['name'] = 'Grand Total';

        $count_region  =   0;
        /*echo'<pre>';
        print_r($big_array);
        die();*/
        foreach($big_array as $key => $region){
            $count_region++;
            $region_sum = $temp_sum;
            $region_sum['id'] = $key;
            $region_sum['name'] = isset($regions[$key]) ? ($regions[$key]) : '';

            $count_area = 0;
            $count  =   0;
            foreach ($region as $key_area => $area){
                $count_area++;
                $flag = true;
                $area_sum = $temp_sum;
                $area_sum['id']     = $key_area;
                $area_sum['name'] = isset($areas[$key_area]) ? ($areas[$key_area]) : '';
                /*$area_code = Areas::find()->where(['id'=>$key_area])->asArray()->one();
                $area_sum['branch_code'] = isset($area_code['code']) ? ($area_code['code']) : '';*/
                $count_branch =0;
                /*echo'<pre>';
                print_r($branches);
                die();*/
                foreach($area as $b_key => $branch) {
                    $count_branch++;
                   // unset($branch['progress_report_id']);
                    //unset($branch['division_id']);
                    unset($branch['region_id']);
                    unset($branch['area_id']);
                    $branch['name'] = isset($branches[$branch['branch_id']])?$branches[$branch['branch_id']]:'';
                    unset($branch['branch_id']);

                    $grand_sum['no_of_loans']          += $branch['no_of_loans'];
                    $grand_sum['amount_disbursed']         += $branch['amount_disbursed'];
                    $grand_sum['monthly_recovery']         += $branch['monthly_recovery'];
                    $grand_sum['funds_required']         += $branch['funds_required'];
                    $grand_sum['actual_recovery']         += $branch['actual_recovery'];
                    $grand_sum['actual_disbursement']         += $branch['actual_disbursement'];

                    //$grand_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $region_sum['no_of_loans']          += $branch['no_of_loans'];
                    $region_sum['amount_disbursed']         += $branch['amount_disbursed'];
                    $region_sum['monthly_recovery']         += $branch['monthly_recovery'];
                    $region_sum['funds_required']         += $branch['funds_required'];
                    $region_sum['actual_recovery']         += $branch['actual_recovery'];
                    $region_sum['actual_disbursement']         += $branch['actual_disbursement'];
                    //$region_sum['recovery_percentage']  += $branch['recovery_percentage'];

                    $area_sum['no_of_loans']            += $branch['no_of_loans'];
                    $area_sum['amount_disbursed']           += $branch['amount_disbursed'];
                    $area_sum['monthly_recovery']           += $branch['monthly_recovery'];
                    $area_sum['funds_required']           += $branch['funds_required'];
                    $area_sum['actual_recovery']         += $branch['actual_recovery'];
                    $area_sum['actual_disbursement']         += $branch['actual_disbursement'];
                    $area_sum['children'][$b_key]               = $branch;
                }



                $region_sum['children'][$count] = $area_sum;
                /*if($area_sum['amount_disbursed'] == 0 && $area_sum['monthly_recovery'] == 0) {
                    $flag = false;
                } else {*/
                    $count++;
                //}

            }



            //$new_big_array[] = $region_sum;
            //$new_big_array[] = $area_sum;
            /*if($this->Session->read('Auth.User.Designation.name') == 'Area Accountant' || $this->Session->read('Auth.User.Designation.name') == 'DEO'){
                $new_big_array[] = $area_sum;
            }
            else{
                $new_big_array[] = $region_sum;
            }*/
            /*if($flag) {*/
                $new_big_array[] = $region_sum;
            /*}*/
        }

      /*  $grand_sum['par_percentage'] = round($grand_sum['par_percentage']/$count_region,4);
        $grand_sum['overdue_percentage'] = round($grand_sum['overdue_percentage']/$count_region,4);
        $grand_sum['recovery_percentage'] = round($grand_sum['recovery_percentage']/$count_region,4);*/

        $new_big_array[] = $grand_sum;
        /*echo'<pre>';
        print_r($new_big_array);
        die();*/
        $progress_report = json_encode($new_big_array);

        /*echo '<pre>';
        print_r($progress_report);
        die();*/

        return $progress_report;
    }

}