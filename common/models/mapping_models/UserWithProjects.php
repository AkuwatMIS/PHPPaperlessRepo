<?php

namespace common\models\mapping_models;

use common\models\Accounts;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Projects;
use common\models\UserProjectsMapping;
use common\models\Users;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branch_projects".
 *
 * @property int $id
 * @property int $project_id
 * @property int $branch_id
 */
class UserWithProjects extends Users
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
            'project_id' => 'Accounts',
        ]);
    }
    /**
     * load the post's categories (*2)
     */
    public function loadProjects($user_id)
    {
        $this->project_ids = array();
        if (!empty($user_id)) {
            $rows = UserProjectsMapping::find()
                ->select(['project_id'])
                ->where(['user_id' => $user_id])
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
    public function saveProjects($user_id)
    {
        /* clear the categories of the post before saving */
        /*echo '<pre>';
        print_r($this);
        die();*/
        UserProjectsMapping::deleteAll(['user_id' => $user_id]);
        //print_r($branch_id);
        //die("we die here");
        if (is_array($this->project_ids)) {
            foreach($this->project_ids as $project_id) {
                $user_project = new UserProjectsMapping();
                $user_project->user_id = $user_id;
                $user_project->project_id = $project_id;


                $user_project->save();
            }
        }
        /* Be careful, $this->category_ids can be empty */
    }
}
