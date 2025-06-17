<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 12/13/2017
 * Time: 11:55 AM
 */
namespace common\rules;

use common\models\Regions;
use common\models\UserStructureMapping;
use yii\rbac\Rule;
use Yii;

/**
 * Checks if authorID matches user passed via params
 */
class IsDivision extends Rule
{
    public $name = 'isDivision';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $division_ids = [];
        $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(),'obj_type' => 'division'])->all();
        foreach ($user_divisions as $user_division) {
            $division_ids[] = $user_division->obj_id;
        }
        $region_ids = [];
        $regions = Regions::find()->where(['in','cr_division_id' , $division_ids])->all();
        foreach ($regions as $region) {
            $region_ids[] = $region->id;
        }
        //return isset($params['model']) ? $params['model']->region_id ==  Yii::$app->user->identity->region_id : false;
        if(isset($params['model']) ) {
            return in_array($params['model']->region_id, $region_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
        } else return false;
    }
}