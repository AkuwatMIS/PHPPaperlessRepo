<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 12/13/2017
 * Time: 11:55 AM
 */
namespace common\rules;

use yii\rbac\Rule;
use Yii;
/**
 * Checks if authorID matches user passed via params
 */
class IsOwner extends Rule
{
    public $name = 'isOwner';


    public function execute($user, $item, $params)
    {
        return isset($params['model']) ? $params['model']->id == $user : false;
    }
}