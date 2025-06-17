<?php

namespace frontend\controllers;

use yii\web\Controller;

/**
 * MembersController implements the CRUD actions for Members model.
 */
class InfoController extends Controller
{

    /**
     * Lists all UsersCopy models.
     * @return mixed
     */
    public function actionIndex()
    {
        phpinfo();
        die();
    }

    /**
     * Lists all UsersCopy models.
     * @return mixed
     */
    public function actionMemcached()
    {
        $this->layout = 'main_simple';
        $mem = new \Memcached();
        $mem->addServer("127.0.0.1", 11211);

        $result = $mem->get("blah");

        if ($result) {
            echo $result;
        } else {
            echo "No matching key found.  I'll add that now!";
            $mem->set("blah", "I am data!  I am held in memcached!") or die("Couldn't save anything to memcached...");
        }
        die();
    }

}
