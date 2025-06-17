<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 12/13/2017
 * Time: 11:55 AM
 */
namespace common\rules;

use common\models\UserStructureMapping;
use yii\rbac\Rule;
use Yii;

/**
 * Checks if authorID matches user passed via params
 */
class IsBranch extends Rule
{
    public $name = 'isBranch';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $branch_ids = [];
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(),'obj_type' => 'branch'])->all();
        foreach ($user_branches as $user_branch) {
            $branch_ids[] = $user_branch->obj_id;
        }
        //return isset($params['model']) ? $params['model']->branch_id ==  Yii::$app->user->identity->branch_id : false;
        if(isset($params['model'])) {
            if(get_class($params['model']->owner)=='common\models\Branches'){
                return in_array($params['model']->id, $branch_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
            }else{
                return in_array($params['model']->branch_id, $branch_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
            }
        } else return false;
    }
}