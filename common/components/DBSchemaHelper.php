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
use yii\db\Schema;
use Yii;
use yii\web\Response;

class DBSchemaHelper
{

    private static $sql_to_sqlite = [
        'varchar'=>'TEXT', 'text'=>'TEXT', 'datetime'=>'TEXT', 'date'=>'TEXT', 'char'=>'TEXT','string'=>'TEXT','timestamp'=>'TEXT',
        'int'=>'INTEGER' , 'integer'=>'INTEGER', 'bigint'=>'INTEGER', 'smallint'=>'INTEGER', 'tinyint'=>'INTEGER', 'money'=>'INTEGER', 'decimal'=>'INTEGER',
        'float'=>'REAL',
        'varbinary' => 'BLOB', 'binary' => 'BLOB'
    ];

    public static function getDbSchema()
    {
        $tables = ['members'=>'members','members_phone'=>'members_phone','members_address'=>'members_address',
            'members_email'=>'members_email','applications'=>'applications',
            /*'projects_disabled'=>'projects_disabled',*/
            'projects_tevta' => 'projects_tevta' ,
            'projects_sidb' => 'projects_sidb' ,
            'projects_disabled' => 'projects_disabled' ,
            'projects_agriculture' => 'projects_agriculture' ,
            'appraisals_social' => 'appraisals_social',
            'appraisals_business' => 'appraisals_business',
            'appraisals_agriculture' => 'appraisals_agriculture',
            'appraisals_housing' => 'appraisals_housing'
            ];
        if(isset($tables)) {
            foreach ($tables as $key => $table) {
                if (Yii::$app->db->driverName == 'sqlsrv') {
                    $data[] = self::getTableSchema($table);
                } else if (Yii::$app->db->driverName == 'mysql') {
                    $data[] = self::getTableSchemaMysql($table);
                }
            }
        }

        $schema['name'] = DBHelper::getDBName();
        $schema['version'] = '1';
        $schema['tables'] = $data;
        $db_schema['database'] = $schema;
        return $db_schema;
    }

    /*----------------MYSQL----------------*/
    public static function getTableSchemaMysql($table)
    {
        $relationships = [];
        $table_schema = Yii::$app->db->getTableSchema($table);
        $data['type'] = $table;

        $table_columns = DBHelper::excludeColumns($table_schema->columns);


        $data['attributes'] = self::getTableColumnsMysql($table_columns, $table);


        $sql = 'SELECT 
                TABLE_SCHEMA,                         
                TABLE_NAME,                            
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,                 
                REFERENCED_COLUMN_NAME                
                FROM
                  INFORMATION_SCHEMA.KEY_COLUMN_USAGE  
                WHERE              
                 REFERENCED_TABLE_NAME IS NOT NULL
                 AND
                TABLE_NAME = :table';
        $table_relations = Yii::$app->db
            ->createCommand($sql)
            ->bindParam(':table', $table)
            ->queryAll();

        if(isset($table_relations)) {
            $table_relations = DBHelper::excludeRelations($table_relations);
            foreach ($table_relations as $table_relation) {
                if($table == "appraisals_business_details" && $table_relation['COLUMN_NAME'] == 'application_id' )
                {
                    continue;
                }
                $relations['column'] = $table_relation['COLUMN_NAME'];
                $relations['ref_table'] = $table_relation['REFERENCED_TABLE_NAME'];
                $relations['ref_column']=  $table_relation['REFERENCED_COLUMN_NAME'];
                array_push($relationships,$relations);
            }
        }

        if($relationships != null)
        {
            $data['relations'] = $relationships;
        }

        return $data;
    }

    public static function getTableColumnsMysql($columns, $table)
    {
        $column_array = [];
        $server_tables = ['members', 'applications', 'appraisals_social','appraisals_business','appraisals_housing','appraisals_agriculture'];
        if(in_array($table, $server_tables)) {
            $column_array[] = ['name' => 'server_id', 'data_type' => 'INTEGER', 'constraints' => []];
            $column_array[] = ['name' => 'sync_status', 'data_type' => 'INTEGER', 'constraints' => []];
        }
        if($table == 'members' || $table ==  'appraisals_social' || $table ==  'appraisals_business' || $table ==  'appraisals_agriculture' || $table ==  'appraisals_housing')
        {
            $column_array[] = ['name' => 'is_image_sync', 'data_type' => 'INTEGER', 'constraints' => []];
        }

        if( $table ==  'ba_details')
        {
            $column_array[] = ['name' => 'server_id', 'data_type' => 'INTEGER', 'constraints' => []];
        }

        $sql = 'SHOW INDEX FROM ' . $table . ' WHERE NOT Non_unique';
        $table_indexes = Yii::$app->db
            ->createCommand($sql)
            ->queryAll();

        if(isset($columns)) {
            foreach ($columns as $key => $column_property) {

                $constraints = [];

                if ($column_property->isPrimaryKey) {
                    array_push($constraints, 'PRIMARY KEY');
                }

                if ($column_property->autoIncrement) {
                    array_push($constraints, 'AUTOINCREMENT');
                }

                $new_constraints = ['application_no'];
                if(!in_array($key,$new_constraints)) {
                    if (!$column_property->allowNull) {
                        array_push($constraints, 'NOT NULL');
                    }
                }

                if(isset($column_property->defaultValue))
                {
                    array_push($constraints, 'Default '. $column_property->defaultValue);
                }

                if(isset($table_indexes))
                {
                    foreach ($table_indexes as $table_index) {
                        if ($table_index['Column_name'] == $column_property->name) {
                            array_push($constraints, 'UNIQUE');
                        }
                    }
                }

                if($table == "appraisals_business_details" && ($key == "type" || $key == "application_id"))
                {
                    continue;
                }
               else if($table == "appraisals_business_details" && $key == "assets_list")
                {
                    $column_array[] = [
                        'name' => "fixed_business_assets",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "running_capital",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "business_expenses",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "new_required_assets",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => $constraints,
                    ];
                }

                else if($table == "appraisals_business_details" && $key == "total_amount")
                {
                    $column_array[] = [
                        'name' => "fixed_business_assets_amount",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "running_capital_amount",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "business_expenses_amount",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                    $column_array[] = [
                        'name' => "new_required_assets_amount",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => $constraints,
                    ];
                }

                else if($table == "members_phone" && $key == "phone_type")
                {
                    $column_array[] = [
                        'name' => "phone",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                }
                else if($table == "members_phone" && $key == "phone")
                {
                    $column_array[] = [
                        'name' => "mobile",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => $constraints,
                    ];
                }
                else if($table == "members_address" && $key == "address_type")
                {
                    $column_array[] = [
                        'name' => "business_address",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => [],
                    ];
                }
                else if($table == "members_address" && $key == "address")
                {
                    $column_array[] = [
                        'name' => "home_address",
                        'data_type' => self::getSQLiteMapping($column_property->phpType),
                        'constraints' => $constraints,
                    ];
                }
                else
                {
                    $column_array[] = [
                        'name' => $column_property->name,
                        'data_type' => self::getSQLiteMapping($column_property->type),
                        'constraints' => $constraints,
                    ];
                }
            }
        }

        return $column_array;
    }

    /*----------------MSSQL----------------*/
    public static function getTableSchema($table)
    {
        $relationships = [];
        $table_schema = Yii::$app->db->getTableSchema($table);
        //$sql = 'select TABLE_SCHEMA,TABLE_NAME,CONSTRAINT_TYPE  from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where TABLE_SCHEMA = "paperless" AND TABLE_NAME = :table';
        // $fk = Yii::$app->db->createCommand($sql)->bindParam(':table', $table)->queryAll();


        $data['type'] = $table;
        $table_columns = DBHelper::excludeColumns($table_schema->columns);

        $data['attributes'] = self::getTableColumns($table_columns, $table);

        $sql = 'SELECT
                col1.name AS [column],
                tab2.name AS [referenced_table],
                col2.name AS [referenced_column]
                FROM sys.foreign_key_columns fkc
                INNER JOIN sys.objects obj
                    ON obj.object_id = fkc.constraint_object_id
                INNER JOIN sys.tables tab1
                    ON tab1.object_id = fkc.parent_object_id
                INNER JOIN sys.schemas sch
                    ON tab1.schema_id = sch.schema_id
                INNER JOIN sys.columns col1
                    ON col1.column_id = parent_column_id AND col1.object_id = tab1.object_id
                INNER JOIN sys.tables tab2
                    ON tab2.object_id = fkc.referenced_object_id
                INNER JOIN sys.columns col2
                    ON col2.column_id = referenced_column_id AND col2.object_id = tab2.object_id
                
                where tab1.name = :table';
        $table_relations = Yii::$app->db
            ->createCommand($sql)
            ->bindParam(':table', $table)
            ->queryAll();

        if(isset($table_relations)) {
            $table_relations = DBHelper::excludeRelations($table_relations);
            foreach ($table_relations as $table_relation) {

                $relations['column'] = $table_relation['column'];
                $relations['ref_table'] = $table_relation['referenced_table'];
                $relations['ref_column']=  $table_relation['referenced_column'];
                array_push($relationships,$relations);
            }
        }

        if($relationships != null)
        {
            $data['relations'] = $relationships;
        }

        return $data;
    }

    public static function getTableColumns($columns, $table)
    {
        $column_array = [];
        $sql = 'SELECT is_primary_key,is_unique,COL_NAME(ic.object_id,ic.column_id) AS name 
                FROM sys.indexes AS i  
                INNER JOIN sys.index_columns AS ic   
                ON i.object_id = ic.object_id AND i.index_id = ic.index_id  
                WHERE i.object_id = OBJECT_ID(:table) and (is_primary_key = 1 or is_unique = 1)';
        $table_indexes = Yii::$app->db
            ->createCommand($sql)
            ->bindParam(':table', $table)
            ->queryAll();

        $sql2 = 'SELECT
                sc.name, sc.is_identity
                FROM sys.columns AS sc
	            INNER JOIN sys.extended_properties AS ep
                ON ep.minor_id = sc.column_id
                WHERE sc.[object_id] = OBJECT_ID(:table)';
        $table_identity = Yii::$app->db
            ->createCommand($sql2)
            ->bindParam(':table', $table)
            ->queryAll();


        if(isset($columns)) {
            foreach ($columns as $key => $column_property) {

                $constraints = [];
                $relations = [];

                if(isset($table_indexes)) {
                    foreach ($table_indexes as $table_index) {
                        if ($table_index['name'] == $column_property->name) {
                            if ($table_index['is_primary_key']) {
                                array_push($constraints, 'PRIMARY KEY');

                            }
                            if($table_identity)
                            {
                                if($table_identity[0]['name'] == $column_property->name)
                                {
                                    if($table_identity[0]['is_identity'] == '1')
                                    {
                                        array_push($constraints, 'AUTOINCREMENT');
                                    }

                                }
                            }
                            if ($table_index['is_unique']) {
                                array_push($constraints, 'UNIQUE');
                            }
                            break;
                        }
                    }
                }

                if (!$column_property->allowNull) {
                    array_push($constraints, 'NOT NULL');
                }

                $column_array[] = [
                    'name' => $column_property->name,
                    'data_type' => self::getSQLiteMapping($column_property->dbType),
                    'constraints' => $constraints,
                ];

            }
        }

        return $column_array;
    }

    public static function getSQLiteMapping($dbType)
    {
        return isset(self::$sql_to_sqlite[$dbType])?(self::$sql_to_sqlite[$dbType]):'';

    }

}