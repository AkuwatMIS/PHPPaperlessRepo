<?php

namespace common\models\mapping_models;

use common\models\Accounts;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "branch_projects".
 *
 * @property int $id
 * @property int $project_id
 * @property int $branch_id
 */
class BranchWithAccounts extends Branches
{
    /**
     * @var array IDs of the categories
     */
    public $account_ids = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            // each category_id must exist in category table (*1)
            ['account_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Accounts::className(), 'targetAttribute' => 'id'
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
            'account_ids' => 'Accounts',
        ]);
    }
    /**
     * load the post's categories (*2)
     */
    public function loadAccounts($branch_id)
    {
        $this->account_ids = array();
        if (!empty($branch_id)) {
            $rows = BranchAccountMapping::find()
                ->select(['account_id'])
                ->where(['branch_id' => $branch_id])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->account_ids[] = $row['account_id'];
            }
        }

    }

    /**
     * save the post's categories (*3)
     */
    public function saveAccounts($branch_id)
    {
        /* clear the categories of the post before saving */
        /*echo '<pre>';
        print_r($this);
        die();*/
        BranchAccountMapping::deleteAll(['branch_id' => $branch_id]);
        //print_r($branch_id);
        //die("we die here");
        if (is_array($this->account_ids)) {
            foreach($this->account_ids as $account_id) {
                $branch_account = new BranchAccountMapping();
                $branch_account->branch_id = $branch_id;
                $branch_account->account_id = $account_id;
                $branch_account->assigned_to = 1;
                $branch_account->created_by = 1;
                $branch_account->updated_by = 1;
                $branch_account->created_at = "1";
                $branch_account->updated_at = "1";

                $branch_account->save();
            }
        }
        /* Be careful, $this->category_ids can be empty */
    }
}
