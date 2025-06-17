<?php

namespace backend\controllers;

use common\models\Donations;
use common\models\Model;
use Yii;
use common\models\StructureTransfer;
use common\models\search\StructureTransferSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * StructureTransferController implements the CRUD actions for StructureTransfer model.
 */
class ImportController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all StructureTransfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public static function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function actionImportMdp(){
        if (isset($_POST["import"])) {
            $fileName = $_FILES["file"]["tmp_name"];
            $customerArr = self::csvToArray($fileName);

            $model = new Donations(['scenario' => 'withoutrecovery']);
            $n=count($customerArr);
            $DonationsArray['Donations'] = $customerArr;
            $modelsDonation = array_fill(0, $n, $model);
            for ($i=0;$i<$n;$i++){

                $modelsDonation[$i]  = new Donations(['scenario' => 'withoutrecovery']);

            }

            Model::loadMultiple($modelsDonation, $DonationsArray);

            $errorSaving = [];
            foreach ($modelsDonation as $modelDonation) {
                if($modelDonation->save()){
                    echo 'saved';
                }else{
                    $errorSaving[]=$modelDonation;
                    var_dump($modelDonation->errors);
                };

            }

            return $this->redirect(Yii::$app->request->referrer);
        }

//        $connection=Yii::app()->db;
//        $transaction=$connection->beginTransaction();
//        try
//        {
//
//        }
//        catch(Exception $e) // an exception is raised if a query fails
//        {
//
//
//        }
    }


}
