<?php
/**
 * Created by PhpStorm.
 * User: junaid.fayyaz
 * Date: 2/26/2018
 * Time: 12:27 PM
 */

namespace common\components\Helpers;


class StringHelper
{
    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param  int  $length
     * @return string
     */
    static function getRandom($length = 38)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function encryptMobile($mobile){
        $sub_string = substr($mobile, 5, 4);
        return str_replace($sub_string,'xxxx',$mobile);
    }
    static function dateFormatter($date){
       return date('d M Y', $date);
    }

}