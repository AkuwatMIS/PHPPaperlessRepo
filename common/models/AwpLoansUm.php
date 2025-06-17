<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "awp_loans_um".
 *
 * @property int $id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $active_loans
 * @property int $no_of_um
 * @property int $active_loans_per_um
 */
class AwpLoansUm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'awp_loans_um';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'area_id', 'branch_id', 'active_loans', 'no_of_um', 'active_loans_per_um'], 'required'],
            [['region_id', 'area_id', 'branch_id', 'active_loans', 'no_of_um', 'active_loans_per_um','no_of_branch_managers'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'active_loans' => 'Active Loans',
            'no_of_um' => 'No Of Um',
            'active_loans_per_um' => 'Active Loans Per Um',
        ];
    }
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
}
