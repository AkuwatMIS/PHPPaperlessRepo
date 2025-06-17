<?php
namespace common\widgets\KamyabLedger;

use Yii;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class KamyabLedger extends \yii\bootstrap\Widget
{
    public $model;

    public function init()
    {
        /*echo '<pre>';
        print_r($this->model);
        die('we');*/
        parent::init();
    }

    public function run()
    {
        //die('we die here');
        LedgerAsset::register($this->getView());
        return $this->render('_ledger', ['model' => $this->model]);
    }
}
