<?php
/**
 * Created by PhpStorm.
 * User: junaid.fayyaz
 * Date: 6/6/2018
 * Time: 12:48 PM
 */

namespace frontend\controllers;
use Yii;

use yii\web\Controller;

class MemController extends Controller
{
    public function actionIndex()
    {
        $cache = Yii::$app->cache;
        $key   = 'Mem';
        $data  = $cache->get($key);
        if ($data === false) {
            $key  = 'Mem';
            $data = 'My First Memcached Data';
            $cache->set($key, $data);
        }
        echo $data;
    }
}