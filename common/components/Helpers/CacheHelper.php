<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\Users;
use common\models\BranchProjectsMapping;
use Yii;

class CacheHelper
{
    static public function setData($data, $key, $time = 3600)
    {
        \Yii::$app->cache->set($key, $data, $time); // time in seconds to store cache
    }

    
    static public function getData($key)
    {
        return \Yii::$app->cache->get($key);
    }

    //user mapping region,area,branch,team,field,project,mobile_roles,api_branches,api_projects
    static public function setUserIdentity($user_id,$key,$data){
        self::setData($data,"user_$key"."_$user_id");
    }

    static public function getUserIdentity($user_id,$key){
        return self::getData("user_$key"."_$user_id");
    }

    //dynamic forms schema
    static public function setFormCache($key,$data){
        self::setData($data,"form_$key");
    }

    static public function getFormCache($key){
        return self::getData("form_$key");
    }

    //regions,areas,branches,teams,fields,projects,divisions
    static public function setStructure($key,$data){
        self::setData(json_encode($data),"structure_$key",60*60*24);
    }

    static public function getStructure($key){
        return json_decode(self::getData("structure_$key"),true);
    }

    //dynamic form listing
    static public function setFormList($key,$data){
        self::setData($data,"form_list_$key");
    }

    static public function getFormList($key){
        return self::getData("form_list_$key");
    }

    //dropdown listing
    static public function setList($key,$data){
        self::setData($data,"list_$key");
    }

    static public function getList($key){
        return self::getData("list_$key");
    }

    //config
    static public function setConfig($key,$data){
        self::setData($data,"config_$key");
    }

    static public function getConfig($key){
        return self::getData("config_$key");
    }

    //cache reports
    static public function setReports($key,$data){
        self::setData(json_encode($data),"reports_$key",60*60*24);
    }

    static public function getReports($key){
        return json_decode(self::getData("reports_$key"),true);
    }

    static public function setListing($user_id,$key,$data){
        self::setData($data,"user_$key"."_$user_id", 60*30);
    }

    static public function getListing($user_id,$key){
        return self::getData("user_$key"."_$user_id");
    }
}