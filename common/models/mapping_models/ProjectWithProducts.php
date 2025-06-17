<?php

namespace common\models\mapping_models;

use common\models\Accounts;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Products;
use common\models\ProjectProductMapping;
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
class ProjectWithProducts extends Projects
{
    /**
     * @var array IDs of the categories
     */
    public $product_ids = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            // each category_id must exist in category table (*1)
            ['product_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Products::className(), 'targetAttribute' => 'id'
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
            'product_id' => 'Products',
        ]);
    }
    /**
     * load the post's categories (*2)
     */
    public function loadProducts($project_id)
    {
        $this->product_ids = array();
        if (!empty($project_id)) {
            $rows = ProjectProductMapping::find()
                ->select(['product_id'])
                ->where(['project_id' => $project_id])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->product_ids[] = $row['product_id'];
            }
        }

    }
    /**
     * save the post's categories (*3)
     */
    public function saveProducts($project_id)
    {
        ProjectProductMapping::deleteAll(['project_id' => $project_id]);
        if (is_array($this->product_ids)) {
            foreach($this->product_ids as $product_id) {
                $project_product= new ProjectProductMapping();
                $project_product->project_id = $project_id;
                $project_product->product_id = $product_id;


                $project_product->save();
            }
        }
    }
}
