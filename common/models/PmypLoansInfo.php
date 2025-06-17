<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pmyp_loans_info".
 *
 * @property int $id
 * @property int $branch_id
 * @property int $loan_id
 * @property int $amount
 * @property int $trns_count
 * @property int $region_id
 * @property int $area_id
 * @property string $status
 * @property string $response
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Branches $branch
 */
class PmypLoansInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pmyp_loans_info';
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
            [['loan_id', 'amount', 'trns_count', 'region_id', 'area_id','branch_id'], 'integer'],
            [['status'], 'string', 'max' => 255],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Sanction No',
            'status' => 'Loan Status',
            'amount' => 'Loan Amount',
            'response' => 'Response',
            'trns_count' => 'Pushed Count',
            'region_id' => 'Region',
            'area_id' => 'Area',
            'branch_id' => 'Branches',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasMany(Loans::className(), ['id' => 'loan_id']);
    }

}
