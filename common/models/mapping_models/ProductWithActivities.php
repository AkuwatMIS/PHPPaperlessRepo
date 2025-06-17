<?php

namespace common\models\mapping_models;

use common\models\Accounts;
use common\models\Activities;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\ProductActivityMapping;
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
class ProductWithActivities extends Products
{
    /**
     * @var array IDs of the categories
     */
    public $activity_ids = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            // each category_id must exist in category table (*1)
            ['$activity_ids', 'each', 'rule' => [
                'exist', 'targetClass' => Activities::className(), 'targetAttribute' => 'id'
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
    public function loadActivities($product_id)
    {
        $this->activity_ids = array();
        if (!empty($product_id)) {
            $rows = ProductActivityMapping::find()
                ->select(['activity_id'])
                ->where(['product_id' => $product_id])
                ->asArray()
                ->all();
            foreach($rows as $row) {
                $this->activity_ids[] = $row['activity_id'];
            }
        }

    }
    /**
     * save the post's categories (*3)
     */
    public function saveActivities($product_id)
    {
        ProductActivityMapping::deleteAll(['product_id' => $product_id]);
        if (is_array($this->activity_ids)) {
            foreach($this->activity_ids as $activity_id) {
                $product_activity= new ProductActivityMapping();
                $product_activity->product_id = $product_id;
                $product_activity->activity_id = $activity_id;


                $product_activity->save();
            }
        }
    }
}
