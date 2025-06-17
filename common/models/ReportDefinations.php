<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "report_definations".
 *
 * @property int $id
 * @property string $name
 * @property string $sql_query
 * @property string $type
 * @property int $cron_detail
 */
class ReportDefinations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_definations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'sql_query', 'type'], 'required'],
            [['sql_query'], 'string'],
            [['cron_detail'], 'integer'],
            [['name'], 'string', 'max' => 150],
            [['type'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sql_query' => 'Sql Query',
            'type' => 'Type',
            'cron_detail' => 'Cron Detail',
        ];
    }
}
