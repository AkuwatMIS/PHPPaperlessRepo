<?php
use yii\helpers\Html;
use common\widgets\LedgerCharges\LedgerCharges;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */

//print_r($model->borrower->group);
//die("we die here");
$this->title = 'Loans';
/*$this->params['breadcrumbs'][] = $this->title;*/
?>
<div class="ledger-view">

<?php
    if ($model->project_id == 77) {
        echo \common\widgets\KamyabLedger\KamyabLedger::widget(['model' => $model]);
    } else
        if(in_array($model->project_id, \common\components\Helpers\StructureHelper::tranchesProjects())) {
        echo LedgerCharges::widget(['model' => $model]);

    } else {
        echo \common\widgets\Ledger\Ledger::widget(['model' => $model]);

    }
?>



</div>
