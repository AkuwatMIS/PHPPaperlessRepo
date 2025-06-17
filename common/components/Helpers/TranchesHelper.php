<?php

/**
 * Created by PhpStorm.
 * User: Junaid Fayyaz
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;



use common\components\DataListHelper;
use common\models\ConfigRules;
use common\models\Lists;
use common\models\Projects;

class TranchesHelper
{
    public static function getTranchesData()
    {
        $response = [];
        $projects = Projects::find()->all();
        foreach ($projects as $project)
        {
            $response_data = [];
            if($project->no_of_tranches == 1) {
                $data['min'] = "5000";
                $data['max'] =  (string)$project->loan_amount_limit;
                $data['service_charges'] = $project->charges_percent;
                $data['tranches'] = $project->no_of_tranches;
                $data['percent'] = "100";
                $response_data = [$data];
            } else {
                $data1['service_charges'] = $project->charges_percent;
                $data2['service_charges'] = $project->charges_percent;
                $rules = ConfigRules::find()->where(['group' => 'tranches'])->all();
                foreach ($rules as $rule)
                {
                    if($rule->field_name == 1)
                    {
                        $data1['tranches'] = $rule->field_name;
                        $data1[$rule->key] =  (string)$rule->value;
                    } else {
                        $data2['tranches'] = $rule->field_name;
                        $data2[$rule->key] =  (string)$rule->value;
                    }
                }

                $d[] = $data1 ;
                $d[] = $data2 ;
                $response_data = $d;
            }
            $response[$project->name] = $response_data;

        }
        return $response;
    }

    public static function saveTranchesFile($tranches_data)
    {
        \Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../'));
        $file_name = 'tranches.json';
        $file_base_path = \Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/';
        $file_path = \Yii::getAlias('@anyname') . '/frontend/web' . '/uploads/tranches/' . $file_name;

        file_put_contents($file_path,json_encode($tranches_data));

        $housing_items = Lists::find()->where(['list_name' => 'housing_items'])->orderBy('sort_order')->asArray()->all();
        $housing_items_list = DataListHelper::getList($housing_items);
        $tranches_data['housing_items'] = $housing_items_list;
        $gz_file_name = 'tranches.json.gz';
        //$data = IMPLODE("",json_encode($tranches_data));
        $gzdata = GZENCODE(json_encode($tranches_data), 9);
        $fp = FOPEN($file_base_path.$gz_file_name, "w");
        FWRITE($fp, $gzdata);
        FCLOSE($fp);

    }
}