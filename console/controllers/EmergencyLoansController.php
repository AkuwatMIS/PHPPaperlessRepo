<?php
namespace console\controllers;

use common\components\Helpers\AccountsReportHelper;
use common\models\ArcAccountReportDetails;
use common\models\ArcAccountReports;
use common\models\Branches;
use common\models\EmergencyLoans;
use common\models\ProgressReports;
use common\models\ProgressReportUpdate;
use common\models\Projects;
use Mpdf\Tag\Progress;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\console\Controller;
use common\models\ProgressReportDetails;
use common\components\Helpers\ProgressReportHelper;
class EmergencyLoansController extends Controller
{
    public function actionUpdate(){
        $emg_loans=EmergencyLoans::find()->all();
        foreach ($emg_loans as $emg){
            $emg->member_id=$emg->loan->application->member_id;
            $emg->city_id=$emg->loan->branch->city_id;
            $emg->save();
        }
    }
}