<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_structure_mapping".
 *
 * @property int $user_id
 * @property int $obj_id
 * @property string $obj_type
 */
class UserStructureMapping extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_structure_mapping';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'obj_id', 'obj_type'], 'required'],
            [['user_id', 'obj_id'], 'integer'],
            [['obj_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'obj_id' => 'Obj ID',
            'obj_type' => 'Obj Type',
        ];
    }

    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'obj_id'])->andOnCondition(['user_structure_mapping.obj_type'=>'branch']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'obj_id'])->andOnCondition(['user_structure_mapping.obj_type'=>'area']);
    }

    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'obj_id'])->andOnCondition(['user_structure_mapping.obj_type'=>'regions']);
    }
    public function getUserBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'obj_id']);
    }
    public function getUserArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'obj_id']);
    }
    public function getUserRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'obj_id']);
    }
    public function getUserTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'obj_id']);
    }
    public function getUserField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'obj_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
