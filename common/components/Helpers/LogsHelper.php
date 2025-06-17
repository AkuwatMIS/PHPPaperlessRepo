<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\components\Parsers\ApiParser;
use common\models\Applications;
use Yii;
//use bedezign\yii2\audit\models\AuditTrail;
use common\models\AuditTrail;

class LogsHelper
{
    public static function getFieldRecord($log_model,$model,$field)
    {
        $count = $log_model::find()->where(['id' => $model->id,'field'=> $field])->count();
        if($count > 0)
        {
            return true;
        }
        else {
            return false;
        }
    }

    public static function getLogs($table, $id)
    {
        $array = explode('_', $table.'Logs');
        $model_name = '';
        foreach ($array as $a) {
            $model_name .= ucfirst($a);
        }
        $model_class = 'common\models\\' . ucfirst($model_name);


        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../'));
        $file_path = Yii::getAlias('@anyname').'/common/models/' . ucfirst($model_name) . '.php';
        if (file_exists(  $file_path)) {
            if($table == 'social_appraisal')
            {
                $application = Applications::findOne(['id' => $id]);
                if(isset($application->socialAppraisal)) {
                    $logs = $model_class::find()->where(['id' => $application->socialAppraisal->id])->orderBy('stamp desc')->all();
                }

            } else if($table == 'business_appraisal')
            {
                $application = Applications::findOne(['id' => $id]);
                if(isset($application->businessAppraisal)) {
                    $logs = $model_class::find()->where(['id' => $application->businessAppraisal->id])->orderBy('stamp desc')->all();
                }
            } else {
                $logs =  $model_class::find()->where(['id' => $id])->orderBy('stamp desc')->all();
            }


           $logs_data =[];
           if(isset($logs) && !empty($logs)) {
               $outer_array = array();
               $unique_array = array();
               foreach ($logs as $key => $value) {
                   $data = ApiParser::parseLog($value);
                   $inner_array = array();
                   $field_name = $value->field;
                   if (!in_array($value->field, $unique_array)) {
                       array_push($unique_array, $field_name);
                       unset($value->field);
                       array_push($inner_array, $data);
                       $arr = [];
                       $arr['name'] = $field_name;
                       $arr['old_value'] = $data['old_value'];
                       $arr['new_value'] = $data['new_value'];
                       $arr['logs'][] = $data;
                       $outer_array[$field_name] = $arr;
                       /*$logs_inner_data = [];
                       $logs_inner_data['logs'][] = $data;
                       $outer_array[$field_name] = $arr;
                       $outer_array[$field_name]['time_line'][] = $logs_inner_data;*/
                   } else {
                       unset($value->field);
                      // array_push($outer_array[$field_name]['time_line'][0]['logs'], $data);
                       array_push($outer_array[$field_name]['logs'], $data);
                   }
               }

               foreach ($outer_array as $arr) {
                   $logs_data[] = $arr;
               }
           }
            return $logs_data;
            /*print_r($logs_data);
            die();*/

        }
    }

    public static function getDiffHtml($model)
    {
        $old = explode("\n", $model->old_value);
        $new = explode("\n", $model->new_value);

        foreach ($old as $i => $line) {
            $old[$i] = rtrim($line, "\r\n");
        }
        foreach ($new as $i => $line) {
            $new[$i] = rtrim($line, "\r\n");
        }

        $diff = new \Diff($old, $new);

        return self::get_decorated_diff($model->old_value, $model->new_value);
        //return $diff->render(new \Diff_Renderer_Html_Inline);
    }

    static function get_decorated_diff($old, $new){
        $from_start = strspn($old ^ $new, "\0");
        $from_end = strspn(strrev($old) ^ strrev($new), "\0");

        $old_end = strlen($old) - $from_end;
        $new_end = strlen($new) - $from_end;

        $start = substr($new, 0, $from_start);
        $end = substr($new, $new_end);
        $new_diff = substr($new, $from_start, $new_end - $from_start);
        $old_diff = substr($old, $from_start, $old_end - $from_start);

        $new = "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
        $old = "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";
        return array("old"=>$old, "new"=>$new);
    }

    static public function htmlDiff($old, $new){
        $ret = '';
        $diff = self::diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
        foreach($diff as $k){
            if(is_array($k))
                $ret .= (!empty($k['d'])?"<del style='background-color:#ffcccc'>".implode(' ',$k['d'])."</del> ":'').
                    (!empty($k['i'])?"<ins style='background-color:#ccffcc'>".implode(' ',$k['i'])."</ins> ":'');
            else $ret .= $k . ' ';
        }
        return $ret;
    }

    static function diff($old, $new){
        $matrix = array();
        $maxlen = 0;
        foreach($old as $oindex => $ovalue){
            $nkeys = array_keys($new, $ovalue);
            foreach($nkeys as $nindex){
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if($matrix[$oindex][$nindex] > $maxlen){
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
            self::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            self::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }

}