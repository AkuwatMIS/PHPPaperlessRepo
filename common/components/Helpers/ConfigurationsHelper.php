<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;
use common\models\Accounts;
use common\models\Activities;
use common\models\Areas;
use common\models\AuthItem;
use common\models\Branches;
use common\models\Cities;
use common\models\ConfigRules;
use common\models\Countries;
use common\models\CreditDivisions;
use common\models\Districts;
use common\models\Divisions;
use common\models\Fields;
use common\models\Products;
use common\models\Projects;
use common\models\Provinces;
use common\models\Regions;
use common\models\Teams;
use Yii;
use yii\web\Response;

class ConfigurationsHelper
{
    static public function getConfiggroups(){
        return array("member"=>"Member","application"=>"Application","loan"=>"Looan","group"=>"Group","project"=>"Project","region"=>"Region","area"=>"Area","branch"=>"Branch","team"=>"Team","field"=>"Field","user"=>"User");
    }
    static public function getConfigpriority()
    {
        return array("0"=>"Global","1"=>"Project","2"=>"Region","3"=>"Area","4"=>"Branch","5"=>"Team","6"=>"Field","7"=>"User");
    }static public function getConfigparenttype()
    {
    return array("global"=>"Global","project"=>"Project","region"=>"Region","area"=>"Area","branch"=>"Branch","team"=>"Team","field"=>"Field","user"=>"User");
    }
    static public function getConfigproject(){
        return Projects::find()->all();
    }
    static public function getConfig($parent_id,$group){
      return  ConfigRules::find()->where(['group'=>$group,'parent_id' => $parent_id])->asArray()->all();
    }
    static public function getConfigGlobal($group){
        return  ConfigRules::find()->where(['group'=>$group,"parent_id" =>0])->asArray()->all();
    }
}