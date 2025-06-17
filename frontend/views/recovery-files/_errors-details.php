<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;
use common\models\RecoveryErrors;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\RecoveryFilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="donation-view">
    <div id="ajaxCrudDatatable">

        <table class="table table-condensed" id="table-error" style="text-align: center">
            <thead>
            <th style="text-align: center">#</th>
            <th style="text-align: center">Sanction No</th>
            <th style="text-align: center">CNIC No</th>
            <th style="text-align: center">Credit</th>
            <th style="text-align: center">Error Description</th>
            <th style="text-align: center">Status</th>
            <th style="text-align: center">Edit</th>
            <?php if($model != null) { ?>
            <th style="text-align: center"><?= Html::a('<i class="glyphicon glyphicon-download-alt"></i>', ['exports', 'id'=>$id],
                    ['title'=> 'Export Errors','data-pjax' => '0','target'=>'_blank']); ?></th>
            <?php } ?>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($model as $error_model)
            {
                ?>
                <tr id="error">
                    <td><?php echo $i++ ;?></td>
                    <td><?=$error_model->sanction_no?></td>
                    <td><?=$error_model->cnic?></td>
                    <td><?=$error_model->credit?></td>
                    <td><?php
                        $errors =json_decode($error_model->error_description,true);
                        foreach ($errors as $key => $value) {
                            foreach ($value as $error){
                                echo $error. '<br>';
                            }
                        }
                        ?></td>
                    <td id="status">
                        <?php if($error_model->status == '0') {
                            echo 'Open';
                        } else if($error_model->status == '1') {
                            echo 'In-Process';
                        } else {
                            echo 'Resolved';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if($error_model->status == '0') {
                             echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/recovery-errors/update', 'id'=>$error_model->id],['role'=>'modal-remote','data-toggle'=>'tooltip','title'=>"Edit Error"]);
                        } ?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</div>