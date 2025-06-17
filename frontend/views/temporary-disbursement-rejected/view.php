<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temporary Disbursement Rejected', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="disbursement-rejected-view">

            <h1><?php Html::encode($this->title) ?></h1>

            <p>
                <?php if(in_array(Yii::$app->user->id,[2012,4494,5507]) && $model->status==2)
                { echo Html::a('Reject Temporary Disbursement: <span class="glyphicon glyphicon-remove"></span>',
                    ['update', 'id' => $model->id], ['class' => 'btn btn-danger float-right mb-3']);
                }?>

                <?php if(in_array(Yii::$app->user->id,[2007,2011,5507]) && $model->status==0)
                { echo Html::a('Review Temporary Disbursement Rejection:',
                    ['review', 'id' => $model->id], ['class' => 'btn btn-primary float-right mb-3']);
                }?>

                <?php Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Rejection Status',
                        'value' => function ($model) {
                            if ($model->status == 0) {
                                return 'Pending for Review';
                            }
                            elseif($model->status == 1) {
                                return 'Rejected';
                            } else {
                                return 'Verification Pending';
                            }
                        }
                    ],
                    [
                        'label' => 'Member Name',
                        'filter' => '',
                        'value' => function ($model) {
                            return $model->disbursement->tranch->loan->application->member->full_name;
                        }
                    ],
                    [
                        'label' => 'Member Cnic',
                        'filter' => '',
                        'value' => function ($model) {
                            return $model->disbursement->tranch->loan->application->member->cnic;
                        }
                    ],
                    [
                        'label' => 'Sanction No',
                        'filter' => '',
                        'value' => function ($model) {
                            return $model->disbursement->tranch->loan->sanction_no;
                        }
                    ],
                    [
                        'label' => 'Loan Amount',
                        'value' => function ($model) {
                            return $model->disbursement->tranch->loan->loan_amount;
                        }
                    ],

                    'reject_reason',
                    'tranche_no',
                ],
            ]) ?>

            <?php if(!empty($model->file_path) && $model->file_path!=null){ ?>
                <img src="<?= Yii::$app->request->baseUrl . '/uploads/' . $model->file_path ?>" width="200" />
                <a href="<?= Yii::$app->request->baseUrl . '/uploads/' . $model->file_path ?>">Download
                </a>
            <?php } ?>


        </div>
    </div>
</div>

