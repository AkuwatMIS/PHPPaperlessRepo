<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


class CodeHelper
{
    public static function getCode(){
        return rand(11111,99999);
    }

    public static function getKeyValue($object)
    {
        $exclude_keys = array('id','application_id','member_id','region_id','area_id','branch_id','team_id','field_id');
        $keys = array_keys($object);
        $data = [];
        foreach ($keys as $key)
        {
            if(!in_array($key,$exclude_keys)){
                $key_name = str_replace('_',' ',$key);
                $d['key'] = ucwords($key_name);
                $d['value'] = $object[$key];
                $data[] = $d;
            }
        }
        return $data;
    }
    public static function getKeyValueCode($object)
    {
        $exclude_keys = array('id','application_id','member_id','region_id','area_id','branch_id','team_id','field_id');
        //$keys = array_keys($object);
        $data = [];
        foreach ($object as $key=>$value)
        {

            if(!in_array($key,$exclude_keys)){
                $key_name = str_replace('_',' ',$key);
                $d['key'] = ucwords($key_name);
                $d['value'] = $object->$key;

                $data[] = $d;
            }

        }
        /*print_r($data);
        die();*/
        return $data;
    }
}