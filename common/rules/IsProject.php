<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 12/13/2017
 * Time: 11:55 AM
 */
namespace common\rules;

use common\models\UserProjects;
use common\models\UserProjectsMapping;
use yii\rbac\Rule;
use Yii;

/**
 * Checks if authorID matches user passed via params
 */
class IsProject extends Rule
{
    public $name = 'isProject';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $project_ids = [];
        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        foreach ($user_projects as $user_project) {
            $project_ids[] = $user_project->project_id;
        }
        if(isset($params['model'])) {
            return  in_array($params['model']->project_id, $project_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
        } else return false;
    }
}