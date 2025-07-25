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
class IsField extends Rule
{
    public $name = 'isField';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $field_ids = [];
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(),'obj_type' => 'field'])->all();
        foreach ($user_fields as $user_field) {
            $field_ids[] = $user_field->obj_id;
        }
        // return isset($params['model']) ? in_array($params['model']->area_id, $area_ids) : false;
        if(isset($params['model'])) {
            return in_array($params['model']->field_id, $field_ids) || ($params['model']->assigned_to ==  Yii::$app->user->getId());
        } else return false;
    }
}