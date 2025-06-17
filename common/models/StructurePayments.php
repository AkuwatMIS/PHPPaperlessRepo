<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "structure_payments".
 *
 * @property int $id
 * @property string $type
 * @property int $project_id
 * @property int $province_id
 * @property int $amount
 * @property int $tax_percentage
 * @property int $tax_amount
 * @property int $total_amount
 * @property int $start_date
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Projects[] $project
 * @property Provinces[] $province
 */
class StructurePayments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'structure_payments';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'province_id', 'amount', 'tax_amount', 'tax_percentage', 'total_amount', 'start_date', 'status'], 'integer'],
            [['name', 'assigned_to', 'created_by', 'created_at'], 'required'],
            [['type'], 'string', 'max' => 100],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provinces::className(), 'targetAttribute' => ['province_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province_id' => 'Province ID',
            'project_id' => 'Project ID',
            'type' => 'Fee Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasMany(Projects::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasMany(Provinces::className(), ['id' => 'province_id']);
    }

}
