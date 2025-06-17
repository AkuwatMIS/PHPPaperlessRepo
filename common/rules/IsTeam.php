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
class IsTeam extends Rule
{
    public $name = 'isTeam';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $team_ids = [];
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(),'obj_type' => 'team'])->all();
        foreach ($user_teams as $user_team) {
            $team_ids[] = $user_team->obj_id;
        }
        // return isset($params['model']) ? in_array($params['model']->area_id, $area_ids) : false;
        if(isset($params['model'])) {
            return in_array($params['model']->team_id, $team_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
        } else return false;
    }
}