<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Loan Write Offs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4> Write Off Status:
                        <?php if ($model->status == 1) { ?>
                            Approved
                        <?php }elseif ($model->status == 2) { ?>
                            Rejected
                       <?php }else{ ?>
                            Pending
                       <?php }
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'loan.application.member.full_name',
            'loan.application.member.cnic',
            'loan.sanction_no',
            'amount',
            'cheque_no',
            'voucher_no',
            'bank_name',
            'bank_account_no',
            ['attribute'=>'type',
                'value'=>function($data){
                    if($data->type==0){
                        return'Recovery';
                    }else{
                        return 'Funeral Charges';
                    }

                }
            ],
            ['attribute'=>'reason',
                'value'=>function($data){
                    if($data->reason=='disable'){
                        return'Permanently Disable';
                    }else{
                        return 'Death';
                    }

                }
            ],
        ],
    ]) ?>
</div>
