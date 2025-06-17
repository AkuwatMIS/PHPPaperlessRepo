<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components;
use common\models\Applications;
use common\models\Loans;
use common\models\SectionFieldsConfigs;
use common\models\ViewSectionFields;
use common\models\ViewSections;
use common\models\ViewSectionsFields;
use yii\db\Schema;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class DBHelper
{

    public static function getDynamicTables()
    {
        $tables_list = [];
        $dbName = self::getDBName();

        $like = 'project|appraisal';
        $not_like = '%details%';

        if (Yii::$app->db->driverName == 'sqlsrv') {
            $sql = 'SELECT TABLE_NAME 
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_CATALOG = :dbname AND TABLE_NAME REGEXP :tables';
        } else if (Yii::$app->db->driverName == 'mysql') {
            $sql = 'SELECT TABLE_NAME 
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME REGEXP :tables AND TABLE_NAME NOT LIKE :t' ;
        }
        $tables = Yii::$app->db
            ->createCommand($sql)
            ->bindParam(':dbname', $dbName)
            ->bindParam(':tables', $like)
            ->bindParam(':t', $not_like)
            ->queryAll();
        foreach ($tables as $table_names)
        {
            foreach ($table_names as $table_name) {

                $tables_list[$table_name] = $table_name;
            }
        }
        ksort($tables_list);
        return self::excludeTables($tables_list);
    }

    public static function getDBTables()
    {
        $tables_list = [];
        $dbName = self::getDBName();

        if (Yii::$app->db->driverName == 'sqlsrv') {
            $sql = 'SELECT TABLE_NAME 
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_CATALOG = :dbname';
        } else if (Yii::$app->db->driverName == 'mysql') {
            $sql = 'SELECT TABLE_NAME 
                FROM  INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = :dbname';
        }
        $tables = Yii::$app->db
            ->createCommand($sql)
            ->bindParam(':dbname', $dbName)
            ->queryAll();
        foreach ($tables as $table_names)
        {
            foreach ($table_names as $table_name) {

                $tables_list[$table_name] = $table_name;
            }
        }
        ksort($tables_list);
        return self::excludeTables($tables_list);
    }

    public static function getTableColumns($table)
    {
        $columns_list = [];
        $columns = Yii::$app->db->getTableSchema($table)->getColumnNames();
        foreach ($columns as $column)
        {
            $columns_list[]= ['id' => $column,
                'name' => $column];
        }

        return $columns_list;
    }

    private static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    static public function getDBName()
    {
        $db = Yii::$app->getDb();
        if (Yii::$app->db->driverName == 'sqlsrv') {
            $dbName = self::getDsnAttribute('Database', $db->dsn);
        } else if (Yii::$app->db->driverName == 'mysql') {
            $dbName = self::getDsnAttribute('dbname', $db->dsn);
        }
        return $dbName;
    }
    static public function excludeTables($array){
        $excluded_tables = ['sysdiagrams','models','lists','member_logs','operation_logs','recovery_logs','view_sections',
            'view_section_fields','section_fields_configs','actions','application_logs','auth_assignment',
            'auth_item','auth_item_child','auth_rule','donation_logs','loan_logs'];
        $result = array_diff($array,$excluded_tables);
        return $result;
    }

    static public function getViewSections(){
        return ViewSections::find()->all();
    }

    static public function getParentId($section,$table, $field){
        $field = ViewSectionFields::find()->where(['section_id'=>$section, 'table_name' => $table,'field'=> $field])->asArray()->one();
        return SectionFieldsConfigs::find()->select(['id', 'key_name as name'])->where(['field_id' => $field['id'],'value' => null])->asArray()->all();
    }

    static public function getParent($section,$table, $field, $key_id){
        $field = ViewSectionFields::find()->where(['section_id'=>$section, 'table_name' => $table,'field'=> $field])->asArray()->one();
        $parent = SectionFieldsConfigs::find()->where(['id' => $key_id,'field_id' => $field['id']])->asArray()->one();
        return SectionFieldsConfigs::find()->select(['id', 'key_name as name'])->where(['id' => $parent['parent_id']])->asArray()->one();
    }

    static public function excludeColumns($array){
        $excluded_columns = ['assigned_to','created_at','updated_at','created_by','updated_by','is_lock','deleted','poverty_index','approved_by','approved_on',
            'is_current', 'no_of_times', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id','deleted_by',
            'deleted_at','left_index','right_index','left_thumb','right_thumb','dob_old','dt_entry_old','team_name',
            'dt_applied_old','bm_verify_latitude','bm_verify_longitude','is_biometric','group_id','field_area_id','platform','application_date'];
        $result = array_diff_key($array,array_flip($excluded_columns));
        return $result;
    }

    static public function excludeRelations($array){
        $excluded_relations = ['project_id','activity_id','product_id','region_id','area_id','branch_id'];
        $result = [];
        foreach ($array as $data)
        {
            if(isset($data['column']))
            {
                if(!in_array($data['column'],$excluded_relations))
                {
                    array_push($result,$data);
                }
            }

            if(isset($data['COLUMN_NAME']))
            {
                if(!in_array($data['COLUMN_NAME'],$excluded_relations))
                {
                    array_push($result,$data);
                }
            }

        }
        return $result;
    }

    public static function getTableColumnsForFilters($table)
    {
        $columns_list = [];
        /*$columns = Yii::$app->db->getTableSchema($table)->getColumnNames();
        $exlude_columns = ['id'];
        foreach ($columns as $column)
        {
            if(!in_array($column,$exlude_columns)) {
                $c = explode('_',$column);
                if(!empty($c[1]) && $c[1] == 'id')
                {
                    $column_name = ucfirst($c[0]);
                } else {
                    $column_name = str_replace('_', ' ', $column);
                    $column_name = ucwords($column_name);
                }
                $columns_list[] = ['index' => $column,
                    'name' => $column_name];
            }
        }*/
        $columns_list = [['name' => 'Member','index' => 'member_id'],['name' => 'Application No','index' => 'application_no'],
            ['name' => 'Project','index' => 'project_id'],['name' => 'Activity','index' => 'activity_id'],
            ['name' => 'Business Condition','index' => 'bzns_cond'],['name' => 'Requested Amount','index' => 'req_amount']];

        return $columns_list;
    }
}