<?php

namespace common\models\mapping_models;

use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\Projects;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branch_projects".
 *
 * @property int $id
 * @property int $project_id
 * @property int $branch_id
 */
class BranchWithProjects extends Branches
{
    /**
     * @var array IDs of the categories
     */
    public $project_ids = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            // each category_id must exist in category table (*1)
            ['project_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Projects::className(), 'targetAttribute' => 'id'
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
            'project_ids' => 'Projects',
        ]);
    }
    /**
     * load the post's categories (*2)
     */
    public function loadProjects($branch_id)
    {
        $this->project_ids = array();
        if (!empty($branch_id)) {
            $rows = BranchProjectsMapping::find()
                ->select(['project_id'])
                ->where(['branch_id' => $branch_id])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->project_ids[] = $row['project_id'];
            }
        }

    }

    /**
     * save the post's categories (*3)
     */
    public function saveProjects($branch_id)
    {
        /* clear the categories of the post before saving */
        /*echo '<pre>';
        print_r($this);
        die();*/
        BranchProjectsMapping::deleteAll(['branch_id' => $branch_id]);
        //print_r($branch_id);
        //die("we die here");
        if (is_array($this->project_ids)) {
            foreach($this->project_ids as $project_id) {
                $branch_project = new BranchProjectsMapping();
                $branch_project->branch_id = $branch_id;
                $branch_project->project_id = $project_id;
                $branch_project->project_id = $project_id;
                $branch_project->assigned_to = 1;
                $branch_project->created_by = 1;
                $branch_project->created_at = "1";
                $branch_project->updated_at = "1";
                $branch_project->account_id = "0";

                $branch_project->save();
            }
        }
        /* Be careful, $this->category_ids can be empty */
    }
}
