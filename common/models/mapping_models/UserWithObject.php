<?php

namespace common\models\mapping_models;

use common\models\Accounts;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Projects;
use common\models\Regions;
use common\models\UserProjectsMapping;
use common\models\Users;
use common\models\UserStructureMapping;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branch_projects".
 *
 * @property int $id
 * @property int $project_id
 * @property int $branch_id
 */
class UserWithObject extends Users
{
    /**
     * @var array IDs of the categories
     */
    public $obj_ids = [];
    public $branch_ids = [];
    public $area_ids = [];
    public $region_ids = [];
    public $team_ids = [];
    public $field_ids = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            // each category_id must exist in category table (*1)
            ['obj_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Branches::className(), 'targetAttribute' => 'id'
            ]
            ],
        ]);
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'obj_ids' => 'Branches',
        ]);
    }

    public function loadBranches($user_id)
    {
    $this->obj_ids = array();
    if (!empty($user_id)) {
        $rows = UserStructureMapping::find()
            ->select(['obj_id'])
            ->where(['user_id' => $user_id,'obj_type'=>'branch'])
            ->asArray()
            ->all();
        foreach($rows as $row) {
            $this->branch_ids[] = $row['obj_id'];
        }
      }

    }
    public function loadAreas($user_id)
    {
        $this->obj_ids = array();
        if (!empty($user_id)) {
            $rows = UserStructureMapping::find()
                ->select(['obj_id'])
                ->where(['user_id' => $user_id,'obj_type'=>'area'])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->area_ids[] = $row['obj_id'];
            }
        }

    }
    public function loadRegions($user_id)
    {
        $this->obj_ids = array();
        if (!empty($user_id)) {
            $rows = UserStructureMapping::find()
                ->select(['obj_id'])
                ->where(['user_id' => $user_id,'obj_type'=>'region'])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->region_ids[] = $row['obj_id'];
            }
        }

    }

    public function saveBranches($user_id)
    {

        UserStructureMapping::deleteAll(['user_id' => $user_id,'obj_type'=>'branch']);
        if (is_array($this->obj_ids)) {
            foreach($this->obj_ids as $obj_id) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $user_id;
                $user_obj->obj_id = $obj_id;
                $user_obj->obj_type = 'branch';
                $user_obj->save();
            }
        }
    }
    public function saveAreas($user_id)
    {
        UserStructureMapping::deleteAll(['user_id' => $user_id,'obj_type'=>'area']);
        if (is_array($this->obj_ids)) {
            foreach($this->obj_ids as $obj_id) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $user_id;
                $user_obj->obj_id = $obj_id;
                $user_obj->obj_type = 'area';
                $user_obj->save();
            }
        }
    }
    public function saveRegions($user_id)
    {
        UserStructureMapping::deleteAll(['user_id' => $user_id,'obj_type'=>'region']);
        if (is_array($this->obj_ids)) {
            foreach($this->obj_ids as $obj_id) {
                $user_obj = new UserStructureMapping();
                $user_obj->user_id = $user_id;
                $user_obj->obj_id = $obj_id;
                $user_obj->obj_type = 'region';
                $user_obj->save();
            }
        }
    }
}
