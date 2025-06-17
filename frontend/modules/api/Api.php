<?php

namespace frontend\modules\api;
use Yii;

/**
 * test module definition class
 */
class Api extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Disable sessions for API requests
        Yii::$app->user->enableSession = false;
    }
}
