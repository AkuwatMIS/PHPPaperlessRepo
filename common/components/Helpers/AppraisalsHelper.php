<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\components\DBHelper;
use common\components\Parsers\ApiParser;
use common\models\Appraisals;
use common\models\BaAssets;
use common\models\BaBusinessExpenses;
use common\models\BaDetails;
use common\models\BaFixedBusinessAssets;
use common\models\BaNewRequiredAssets;
use common\models\BaRunningCapital;
use common\models\AppraisalsBusiness;
use common\models\Images;
use common\models\ProjectAppraisalsMapping;
use common\models\Projects;
use common\models\SocialAppraisal;
use common\models\SocialAppraisalDiseasesMapping;
use yii\helpers\ArrayHelper;

class AppraisalsHelper
{
    public static function getChildDiseases($id)
    {
        $child_diseases = [];
        $diseases = SocialAppraisalDiseasesMapping::find()->select(['disease_id'])->where(['social_appraisal_id' => $id, 'type' => 'child'])->all();
        foreach ($diseases as $disease)
        {
            $child_diseases[] = $disease['disease_id'];
        }
        return $child_diseases;
    }

    public static function getAdultDiseases($id)
    {
        $adult_diseases = [];
        $diseases = SocialAppraisalDiseasesMapping::find()->select(['disease_id'])->where(['social_appraisal_id' => $id, 'type' => 'adult'])->all();
        foreach ($diseases as $disease)
        {
            $adult_diseases[] = $disease['disease_id'];
        }
        return $adult_diseases;
    }

    public static function getDiseases($id)
    {
        $diseases_list = [];
        $diseases = SocialAppraisalDiseasesMapping::find()->select(['disease_id','type'])->where(['social_appraisal_id' => $id])->all();
        foreach ($diseases as $disease)
        {
            $data['type'] = $disease['type'];
            $data['disease_id'] = $disease->disease->name;
            //$data['disease_id'] = $disease['disease_id'];
            $diseases_list[] = $data;
        }
        return $diseases_list;
    }

    public static function getAssets($id)
    {
        $ba_assets_data = [];
        $ba_assets = BaAssets::find()->where(['ba_id' => $id])->all();
        if(isset($ba_assets))
        {
            $ba_assets_data = ApiParser::parseAssets($ba_assets);
        }
        return $ba_assets_data;
    }

    public static function getFixedBusinessAssets($id)
    {
        $fixed_business_assets_data = [];
        $fixed_business_assets = BaFixedBusinessAssets::find()->where(['ba_id' => $id])->all();
        if(isset($fixed_business_assets))
        {
            $fixed_business_assets_data = ApiParser::parseFixedBusinessAssets($fixed_business_assets);
        }
        return $fixed_business_assets_data;
    }

    public static function getDetails($id)
    {
        $details_data = [];
        $details_model = BaDetails::find()->where(['ba_id' => $id])->one();
        if(isset($details_model))
        {
            $details_data = ApiParser::parseBaDetails($details_model);
        }
        return $details_data;
    }

    public static function getBusinessExpenses($id)
    {
        $business_expenses_data = [];
        $business_expenses = BaBusinessExpenses::find()->where(['ba_id' => $id])->all();
        if(isset($business_expenses))
        {
            $business_expenses_data = ApiParser::parseBusinessExpenses($business_expenses);
        }
        return $business_expenses_data;
    }

    public static function getNewRequiredAssets($id)
    {
        $required_assets_data = [];
        $required_assets = BaNewRequiredAssets::find()->where(['ba_id' => $id])->all();
        if(isset($required_assets))
        {
            $required_assets_data = ApiParser::parseNewRequiredAssets($required_assets);
        }
        return $required_assets_data;
    }

    public static function getRunningCapital($id)
    {
        $running_capital_data = [];
        $running_capital = BaRunningCapital::find()->where(['ba_id' => $id])->all();
        if(isset($running_capital))
        {
            $running_capital_data = ApiParser::parseRunningCapital($running_capital);
        }
        return $running_capital_data;
    }

    public static function getSocialAppraisal($application_id)
    {
        $social_appraisal = SocialAppraisal::findOne(['application_id' => $application_id]);
        return $social_appraisal;
    }

    public static function getBusinessAppraisal($application_id)
    {
        $business_appraisal = AppraisalsBusiness::findOne(['application_id' => $application_id]);
        return $business_appraisal;
    }

    public static function getAppraisalAddress($latitude,$longitude)
    {
        if(!empty(trim($latitude)) && !empty($longitude)){
            $latitude = trim($latitude);
            $longitude = trim($longitude);
            //Send request and receive json data by address
//            $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI&latlng='.trim($latitude).','.trim($longitude).'&sensor=false');
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI&latlng='.$latitude.','.$longitude.'&sensor=false';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($data);
            $status = $output->status;
            //Get address from json data
            $address = ($status=="OK")?(string)$output->results[0]->formatted_address:'Address Not Available';
//            print_r($address);
//            die();
            //Return address of the given latitude and longitude
            if(!empty($address)){
                return $address;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function getSocialAppraisalImage($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'social_appraisal', 'image_type' => 'social_appraisal_image']);
        return $image;
    }

    public static function getBusinessAppraisalImage($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'business_appraisal', 'image_type' => 'business_appraisal_image']);
        return $image;
    }

    public static function getAppraisalsList($controller, $method, $type, $user_id)
    {
        $data = [];
        $data_appraisal = [];
        $projects = \Yii::$app->Permission->getProjectListNameWise($controller, $method, $type, $user_id);
        $ids = array_keys($projects);
       // $projects = Projects::find()->all();
        foreach ($ids as $id) {
            $project_appraisals = ProjectAppraisalsMapping::find()->where(['project_id' => $id])->all();
            if(isset($project_appraisals)) {
                foreach ($project_appraisals as $project_appraisal) {
                    $data_appraisal['name'] = $project_appraisal->appraisal->name;
                    $data_appraisal['table'] = $project_appraisal->appraisal->appraisal_table;
                    $data_appraisal['form'] = $project_appraisal->appraisal->name.'_form';
                    $data_appraisal['data'] = $project_appraisal->appraisal->name.'_data';
                    $data[$projects[$id]][] = $data_appraisal;
                }
            }
        }
        return $data;
    }

    public static function getAppraisalForm()
    {
        $data = [];
        $projects = Projects::find()->all();
        foreach ($projects as $project) {
            $project_appraisals = ProjectAppraisalsMapping::find()->where(['project_id' => $project->id])->all();
            if(isset($project_appraisals)) {
                foreach ($project_appraisals as $project_appraisal) {
                    $data[$project->name][] = $project_appraisal->appraisal->name;
                }
            }
        }
        return $data;
    }
}