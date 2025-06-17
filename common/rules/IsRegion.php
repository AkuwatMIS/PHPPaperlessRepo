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
class IsRegion extends Rule
{
    public $name = 'isRegion';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $region_ids =[];
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(),'obj_type' => 'region'])->all();
        foreach ($user_regions as $user_region) {
            $region_ids[] = $user_region->obj_id;
        }
        //return isset($params['model']) ? $params['model']->region_id ==  Yii::$app->user->identity->region_id : false;
        if(isset($params['model']) ) {
            return in_array($params['model']->region_id, $region_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
        } else return false;
    }
}