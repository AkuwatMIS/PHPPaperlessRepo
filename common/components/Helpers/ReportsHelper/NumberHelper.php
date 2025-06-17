<?php
/**
 * Created by PhpStorm.
 * User: umair.awan
 * Date: 10/2/2017
 * Time: 1:25 PM
 */

namespace common\components\Helpers\ReportsHelper;

use DateTime;
use DateTimeZone;

class NumberHelper
{
    static function getFormattedNumberAmount($number,$unit_type=''){

        //$number_return = 0;
        if($number == 0){
            return isset($number)?$number:0;
        }
        if($number < 1000000){
            $number = number_format($number);
        }else if($number >= 1000000 && $number < 1000000000){
            $number = $number/1000000;
            $number = number_format(round($number,2),2).' m';
        }else if($number >= 1000000000){
            $number = $number/1000000000;
            $number = number_format(round($number,2),2).' b';
        } else {
            return isset($number)?$number:0;
        }
        /*if($unit_type=='m'){
            $number = $number/1000000;
        }

        if($unit_type=='b'){
            $number = $number/1000000000;
        }*/

        return isset($number)?$number:0;
    }
    static function getFormattedNumber($number,$unit_type=''){

        if($number == 0){
            return $number;
        }

        if($unit_type=='m'){
            $number = $number/1000000;
        }

        return number_format(round($number,2));
    }
}