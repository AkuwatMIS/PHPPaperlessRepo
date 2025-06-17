<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auth_rule".
 *
 * @property string $name
 * @property resource $data
 * @property int $created_at
 * @property int $updated_at
 */
class AuthRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getField()
    {
        $column_name = '';
        if($this->name == 'isField')
        {
            $column_name = 'field_id';
        }
        else if($this->name == 'isTeam')
        {
            $column_name = 'team_id';
        }
        else if($this->name == 'isBranch')
        {
            $column_name = 'branch_id';
        }
        else if($this->name == 'isArea')
        {
            $column_name = 'area_id';
        }
        else if($this->name == 'isRegion' || $this->name == 'isDivision')
        {
            $column_name = 'region_id';
        }
        else if($this->name == 'isProject')
        {
            $column_name = 'project_id';
        }

        return $column_name;
    }

    public function getIdList()
    {
        $mapping_ids = [];
        if($this->name == 'isField')
        {
            $fields = Yii::$app->user->identity->fields;
            if(isset($fields)) {
                foreach ($fields as $field) {
                    $mapping_ids[] = $field->obj_id;
                }
            }
        }
        else if($this->name == 'isTeam')
        {
            $teams = Yii::$app->user->identity->teams;
            if(isset($teams)) {
                foreach ($teams as $team) {
                    $mapping_ids[] = $team->obj_id;
                }
            }
        }
        else if($this->name == 'isBranch')
        {
            $branches = Yii::$app->user->identity->branches;
            if(isset($branches)) {
                foreach ($branches as $branch) {
                    $mapping_ids[] = $branch->obj_id;
                }
            }
        }
        else if($this->name == 'isArea')
        {
            $areas = Yii::$app->user->identity->areas;

            if(isset($areas)) {
                foreach ($areas as $area) {
                    $mapping_ids[] = $area->obj_id;
                }
            }
        }
        else if($this->name == 'isRegion')
        {
            $regions = Yii::$app->user->identity->regions;
            if(isset($regions)) {
                foreach ($regions as $region) {
                    $mapping_ids[] = $region->obj_id;
                }
            }
        }
        else if($this->name == 'isDivision')
        {
            $divisions = Yii::$app->user->identity->divisions;

            $division_ids = [];
            if(isset($divisions)) {
                foreach ($divisions as $division) {
                    $division_ids[] = $division->obj_id;
                }
            }

            $regions = Regions::find()->where(['in','cr_division_id' , $division_ids])->all();
            foreach ($regions as $region) {
                $mapping_ids[] = $region->id;
            }
        }
        else if($this->name == 'isProject')
        {
            $projects = Yii::$app->user->identity->projects;
            if(isset($projects)) {
                foreach ($projects as $project) {
                    $mapping_ids[] = $project->project_id;
                }
            }
        }

        return $mapping_ids;
    }

    public function getIdListForReports()
    {
        $mapping_ids = '';
        if($this->name == 'isField')
        {
            $fields = Yii::$app->user->identity->fields;
            if(isset($fields)) {
                foreach ($fields as $field) {
                    $mapping_ids .= $field->obj_id . ',';
                }
            }
        }
        else if($this->name == 'isTeam')
        {
            $teams = Yii::$app->user->identity->teams;
            if(isset($teams)) {
                foreach ($teams as $team) {
                    $mapping_ids .= $team->obj_id . ',';
                }
            }
        }
        else if($this->name == 'isBranch')
        {
            $branches = Yii::$app->user->identity->branches;
            if(isset($branches)) {
                foreach ($branches as $branch) {
                    $mapping_ids .= $branch->obj_id . ',';
                }
            }
        }
        else if($this->name == 'isArea')
        {
            $areas = Yii::$app->user->identity->areas;

            if(isset($areas)) {
                foreach ($areas as $area) {
                    $mapping_ids .= $area->obj_id . ',';
                }
            }
        }
        else if($this->name == 'isRegion')
        {
            $regions = Yii::$app->user->identity->regions;
            if(isset($regions)) {
                foreach ($regions as $region) {
                    $mapping_ids .= $region->obj_id . ',';
                }
            }
        }
        else if($this->name == 'isDivision')
        {
            $divisions = Yii::$app->user->identity->divisions;

            $division_ids = [];
            if(isset($divisions)) {
                foreach ($divisions as $division) {
                    $division_ids[] = $division->obj_id;
                }
            }

            $regions = Regions::find()->where(['in','cr_division_id' , $division_ids])->all();
            foreach ($regions as $region) {
                $mapping_ids .= $region->obj_id . ',';
            }
        }
        else if($this->name == 'isProject')
        {
            $projects = Yii::$app->user->identity->projects;
            if(isset($projects)) {
                foreach ($projects as $project) {
                    $mapping_ids .= $project->project_id . ',';
                }
            }
        }

        return $mapping_ids;
    }

    public function getBranches()
    {
        $list = $this->getIdList();
        if($this->name == 'isField')
        {
            $team_ids = Fields::find()->select(['team_id'])->where(['in','id', $list])->asArray()->all();
            print_r($team_ids);
            die();
            foreach ($team_ids as $team_id) {
                $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $team_id[0]['team_id']])->asArray()->all();
            }
        }
        else if($this->name == 'isTeam')
        {
            $teams = Yii::$app->user->identity->teams;
            if(isset($teams)) {
                foreach ($teams as $team) {
                    $mapping_ids[] = $team->obj_id;
                }
            }
        }
        else if($this->name == 'isBranch')
        {
            $branches = Yii::$app->user->identity->branches;
            if(isset($branches)) {
                foreach ($branches as $branch) {
                    $mapping_ids[] = $branch->obj_id;
                }
            }
        }
        else if($this->name == 'isArea')
        {
            $areas = Yii::$app->user->identity->areas;

            if(isset($areas)) {
                foreach ($areas as $area) {
                    $mapping_ids[] = $area->obj_id;
                }
            }
        }
        else if($this->name == 'isRegion')
        {
            $regions = Yii::$app->user->identity->regions;
            if(isset($regions)) {
                foreach ($regions as $region) {
                    $mapping_ids[] = $region->obj_id;
                }
            }
        }
        else if($this->name == 'isDivision')
        {
            $divisions = Yii::$app->user->identity->divisions;

            $division_ids = [];
            if(isset($divisions)) {
                foreach ($divisions as $division) {
                    $division_ids[] = $division->obj_id;
                }
            }

            $regions = Regions::find()->where(['in','cr_division_id' , $division_ids])->all();
            foreach ($regions as $region) {
                $mapping_ids[] = $region->id;
            }
        }
        else if($this->name == 'isProject')
        {
            $projects = Yii::$app->user->identity->projects;
            if(isset($projects)) {
                foreach ($projects as $project) {
                    $mapping_ids[] = $project->project_id;
                }
            }
        }
    }

    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::className(), ['rule_name' => 'name']);
    }
}
