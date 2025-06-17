<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components;
use common\components\Helpers\CacheHelper;
use common\models\Activities;
use common\models\Applications;
use common\models\Areas;
use common\models\Diseases;
use common\models\Lists;
use common\models\Loans;

use common\models\ProjectProductMapping;
use common\models\Projects;
use common\models\SectionFieldsConfigs;
use common\models\ViewSectionFields;
use common\models\ViewSections;
use yii\db\ActiveQuery;
use yii\db\Schema;
use Yii;
use yii\web\Response;

class DataListHelper
{
    public static function getDataList($section_name,$controller, $method,$type,$user_id='')
    {

       /* $schema = CacheHelper::getFormList($section_name);

        if (empty($schema)) {*/
            $sections = ViewSections::find()->where(['section_table_name' => $section_name])->andWhere(['in', 'type' , ['section','sub_form']])->asArray()->all();
            $schema = [];
            $data = [];
            if (isset($sections)) {
                foreach ($sections as $section) {
                    $section_fields = ViewSectionFields::find()->where(['section_id' => $section['id']])->asArray()->all();
                    if (isset($section_fields)) {
                        foreach ($section_fields as $section_field) {
                            $field_configs = SectionFieldsConfigs::find()->where(['field_id' => $section_field['id'], 'key_name' => 'answers', 'parent_id' => 0])->asArray()->all();
                            if (isset($field_configs)) {
                                foreach ($field_configs as $field_config) {
                                    $list_name = $field_config['value'];
                                    if ($list_name == 'projects') {
                                        // die('here');
                                        $data_list = Yii::$app->Permission->getProjectListNameWise($controller, $method, $type, $user_id);
                                        $data[$list_name] = self::getList($data_list);
                                    }elseif($list_name == 'Banks_accounts'){
                                        $bank_accounts = Lists::find()->where(['list_name' => 'bank_accounts'])->orderBy('sort_order')->asArray()->all();
                                        if(!empty($bank_accounts) && $bank_accounts!=null){
                                            $data['Banks_accounts'] = self::getListData($bank_accounts);
                                        }
                                        $coc_accounts = Lists::find()->where(['list_name' => 'coc_accounts'])->orderBy('sort_order')->asArray()->all();
                                        if(!empty($coc_accounts) && $coc_accounts!=null){
                                            $data['COC_accounts'] = self::getListData($coc_accounts);
                                        }
                                        $cheque_accounts = Lists::find()->where(['list_name' => 'cheque_accounts'])->orderBy('sort_order')->asArray()->all();
                                        if(!empty($cheque_accounts) && $cheque_accounts!=null){
                                            $data['Cheque_accounts'] = self::getListData($cheque_accounts);
                                        }
                                    } else {
                                        $class1 = '/common/models/' . ucfirst($list_name);
                                        $class = '\common\models\\' . ucfirst($list_name);
                                        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
                                        $file_path = Yii::getAlias('@anyname') . $class1 . '.php';
                                        //die($file_path);
                                        if (file_exists($file_path)) {
                                            $data_list = $class::find()->asArray()->all();
                                            $data[$list_name] = self::getData($data_list);
                                        } else {
                                            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
                                            $data[$list_name] = self::getListData($data_list);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data = array_merge($data, self::getDependentDataList($section_name));
            if ($section_name == "appraisals_social" ) {
                $data_list = Yii::$app->Permission->getProjectListNameWise($controller, $method, $type, $user_id);
                $data['projects'] = self::getList($data_list);
            }
            $schema = $data;
            /*CacheHelper::setFormList($section_name, $schema);
        }*/
        /*print_r($schema);
        die('here');*/
        return $schema;
    }

    public static function getTableDataList($table,$controller, $method, $type, $user_id='')
    {
       /* $data = CacheHelper::getList($table);

        if (empty($data)) {*/
            $data = [];
            $table_schema = Yii::$app->db->getTableSchema($table);
            $columns = $table_schema->columns;
            foreach ($columns as $key => $column) {
                if ($table == "business_appraisal" && $key == 'business_type') {
                    $activities = Activities::find()->select(['id', 'name'])->where(['deleted' => 0])->all();
                    if (isset($activities) && !empty($activities)) {
                        $data[$key] = self::getData($activities);
                    }

                }

                $data_list = Lists::find()->where(['list_name' => $key])->orderBy('sort_order')->asArray()->all();

                if ($table == "applications" && $key == 'status') {
                    $data_list = Lists::find()->where(['list_name' => 'applications_status'])->orderBy('sort_order')->asArray()->all();
                }
                if (isset($data_list) && !empty($data_list)) {
                    $data[$key] = self::getListData($data_list);
                }

                if ($table == "applications" && $key == 'reject_reason') {
                    $temp_reject = Lists::find()->where(['list_name' => 'temporary_reject_reason'])->orderBy('sort_order')->asArray()->all();
                    $permanent_reject = Lists::find()->where(['list_name' => 'permanent_reject_reason'])->orderBy('sort_order')->asArray()->all();

                    $data['temporary_reject_reason'] = self::getListData($temp_reject);
                    $data['permanent_reject_reason'] = self::getListData($permanent_reject);
                }
            }
            if ($table == "applications" || $table == "appraisals_social" ) {
                $data_list = Yii::$app->Permission->getProjectListNameWise($controller, $method, $type, $user_id);
                $data['projects'] = self::getList($data_list);
            }
           /* CacheHelper::setList($table, $data);
        }*/
        return $data;

    }

    public static function getDependentDataList($section_name)
    {

        $sections = ViewSections::find()->where(['section_table_name' => $section_name])->asArray()->all();
        $data = [];
        if(isset($sections)) {
            foreach ($sections as $section) {

                $section_fields = ViewSectionFields::find()->where(['section_id' => $section['id']])->asArray()->all();
                if(isset($section_fields)) {
                    foreach ($section_fields as $section_field) {
                        $field_configs = SectionFieldsConfigs::find()->where(['field_id' => $section_field['id'], 'key_name' => 'answers'])->andFilterWhere(['!=', 'parent_id', 0])->asArray()->all();
                        if(isset($field_configs)) {
                            foreach ($field_configs as $field_config) {
                                if ($field_config['value'] == 'projects_products') {
                                    $t = explode('_', $field_config['value']);
                                    $d = $t[0];
                                    $class = 'common\models\\' . ucfirst($d);
                                    $data_list = $class::find()->asArray()->all();
                                    foreach ($data_list as $list) {

                                        $data_list = ProjectProductMapping::find()->where(['project_id' => $list['id']])
                                            ->joinWith(['product' => function (ActiveQuery $query) {
                                                return $query
                                                    ->andOnCondition(['=', 'products.status', 1]);
                                            }])->asArray()->all();
                                        $column_array = [];
                                        foreach ($data_list as $da) {
                                            $name = $da['product']['name'];
                                            $index = (string)$da['product_id'];
                                            if($name!=null){
                                                $column_array[] = [
                                                    'name' => $name,
                                                    'index' => $index,
                                                    //'code' => $code,
                                                ];
                                            }
                                        }
                                        $data[$list['name'] . '_products'] = $column_array;

                                    }
                                } elseif ($field_config['value'] == 'Banks_accounts'){

                                }else {
                                    if (strpos($field_config['value'], '_') == FALSE) {
                                        break;
                                    }
                                    $section_field = ViewSectionFields::find()->where(['id' => $field_config['field_id']])->asArray()->one();
                                    $dependent_attribute = $section_field['field'];

                                    $t = explode('_', $field_config['value']);
                                    $dependent_table = $t[0];
                                    $related_table = $t[1];
                                }
                            }
                        }
                        if (!empty($dependent_table) && !empty($related_table) && !empty($dependent_attribute)) {
                            $class = 'common\models\\' . ucfirst($dependent_table);
                            if (file_exists(Yii::getAlias('@anyname') . '/common/models/' . ucfirst($dependent_table) . '.php')) {

                                $data_list = $class::find()->where(['status'=>1])->asArray()->all();
                                foreach ($data_list as $list) {
                                    $class = 'common\models\\' . ucfirst($related_table);
                                    if (file_exists(Yii::getAlias('@anyname') . '/common/models/' . ucfirst($related_table) . '.php')) {
                                        $dependent_data = $class::find()->where(['status'=>1])->andWhere([$dependent_attribute => $list['id']])->asArray()->all();
                                        $data[$list['name'] . '_' . $related_table] = self::getData($dependent_data);
                                    }

                                }
                            } else {
                                $data_list = Lists::find()->where(['list_name' => $dependent_table])->asArray()->all();
                                foreach ($data_list as $list) {
                                    $dependent_data = Lists::find()->where(['list_name' => $list['value'] . '_' . $related_table])->asArray()->all();
                                    $data[$list['value'] . '_' . $related_table] = self::getListData($dependent_data);
                                }
                            }
                        }

                    }
                }
            }
            if($section_name == 'appraisals_business')
            {
                $data = array_merge($data,self::getBADropdowns());
            }
        }
        return $data;
    }

    public static function getData($data_list)
    {
        $column_array = [];

        foreach ($data_list as $data) {
            $name = '';
            $code = '';
            $index = '';
            if(isset($data['code']))
            {
                $code = $data['code'];
            }
            $name = $data['name'];
            $index = (string)$data['id'];
            $column_array[] = [
                'name' => $name,
                'index' => $index,
                //'code' => $code,
            ];
        }

        return $column_array;
    }


    public static function getListData($data_list)
    {
        $column_array = [];

        foreach ($data_list as $data) {
            $column_array[] = [
                'name' => $data['label'],
                'index' => $data['value'],
            ];
        }

        return $column_array;
    }

    public static function getList($data_list)
    {
        $column_array = [];

        foreach ($data_list as $k => $data) {
            $column_array[] = [
                'name' => $data,
                'index' => (string)$k,
            ];
        }

        return $column_array;
    }

    public static function getListNameIndex($data_list)
    {
        $column_array = [];

        foreach ($data_list as $data) {
            $column_array[] = [
                'index' => $data['value'],
                'name' => $data['label'],
            ];
        }

        return $column_array;
    }

    public static function getListDataKeyValue($data_list)
    {
        $column_array = [];

        foreach ($data_list as $data) {
            $column_array[] = [
                'key' => $data['value'],
                'value' => $data['label'],
            ];
        }

        return $column_array;
    }

    public static function getBADropdowns()
    {
        $activities = Activities::find()->select(['id','name'])->where(['deleted' => 0])->all();
        $response = [];
        $response['activities']= self::getData($activities);
        foreach ($activities as $activity)
        {
            $data = [];
            /*$data['business_type'] = $activity->name;

            $details = [];
            $list_name = $activity['name'] . '-fixed_business_assets';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $details['fixed_business_assets'] = self::getListDataKeyValue($data_list);

            $list_name = $activity['name'] . '-running_capital';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $details['running_capital'] = self::getListDataKeyValue($data_list);

            $list_name = $activity['name'] . '-business_expenses';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $details['business_expenses'] = self::getListDataKeyValue($data_list);

            $list_name = $activity['name'] . '-new_required_assets';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $details['new_required_assets'] = self::getListDataKeyValue($data_list);
            $data['details'] = $details;
            $response[] = $data;*/
            $list_name = $activity['name'] . '-fixed_business_assets';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $response[$activity['name'].'_fixed_assets'] = self::getListNameIndex($data_list);
            $list_name = $activity['name'] . '-running_capital';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $response[$activity['name'].'_running_capital'] = self::getListNameIndex($data_list);
            $list_name = $activity['name'] . '-business_expenses';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $response[$activity['name'].'_business_expenses'] = self::getListNameIndex($data_list);
            $list_name = $activity['name'] . '-new_required_assets';
            $data_list = Lists::find()->where(['list_name' => $list_name])->orderBy('sort_order')->asArray()->all();
            $response[$activity['name'].'_required_assets'] = self::getListNameIndex($data_list);


        }
        return $response;
    }
}