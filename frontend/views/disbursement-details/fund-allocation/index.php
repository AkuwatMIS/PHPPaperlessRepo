<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DisbursementDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Allocate Funding Line';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
//print_r($query);die();
?>
<style>
    #customers {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #customers td, #customers th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #customers tr:nth-child(even){background-color: #f2f2f2;}

    #customers tr:hover {background-color: #ddd;}

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    }
</style>
<div class="container-fluid">

    <!--flash message start-->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i>Batch Created Successfully!</h4>
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>


    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-info alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-checkk"></i></h4>
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <!--flash message ends-->

    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Select From Filters </h6>
        <?php echo $this->render('_searchFundAllocation', [
            'model' => $searchModel,
            //'bank_names' => $bank_names,
            'branches_names' => $branches_names,
            'regions' => $regions,
            'areas' => $areas,
            'query' => $query,
            'bank_name_filter' => $bank_name_filter,
        ]); ?>
      <?php if (!empty($query['count']) && isset($query)) { ?>

          <!--export-->


          <!--export-->
<!--          <div class="dropdown pull-right" style="display: none">-->
<!--              <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">-->
<!--                  <i class="glyphicon glyphicon-export"></i>-->
<!--                  <span class="caret"></span></button>-->
<!---->
<!--              <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">-->
<!--                  <li role="presentation" >-->
<!--                      <form action="/disbursement-details/allocate-funds">-->
<!--                          <input type="hidden" name="batch[]" value="--><?php /* $query['batch']*/ ?><!--">-->
<!--                          <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"-->
<!--                                  role="menuitem" tabindex="-1"><i-->
<!--                                      class="text-primary glyphicon glyphicon-floppy-open"></i> CSV-->
<!--                          </button>-->
<!---->
<!--                      </form>-->
<!--                  </li>-->
<!--              </ul>-->
<!--          </div>-->

            <?= Html::beginForm(['disbursement-details/allocate-funds'], 'post'); ?>
            <div class="row">
                <div class="col-md-12">
                    <table id="customers">
                        <tr>
                            <th>No of Loans/Financing</th>
                            <th>Amount (Rs.)</th>
                            <th>Funding Line</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <td><?= $query['count']?>
                               <input type="hidden" name="count" value="<?= $query['count']?>">
                               <input type="hidden" name="project_id" value="<?= $query['project_id']?>">
                               <input type="hidden" name="batch[]" value="<?= $query['batch'] ?>">
                               <input type="hidden" name="bank_name_filter" value="<?= $bank_name_filter ?>">

                            </td>
                            <td>
                                <input type="hidden" name="sum" value="<?= $query['sum']?>">
                                <?= number_format($query['sum'])?>
                            </td>
                            <td>
                                <?= Html::activedropDownList($searchModel,'fund_id',$funds,  ['class'=>'form-control','prompt' => 'Select Funding Line','style'=>'float:right']) ?>

                            </td>
                            <td>
                                <?=Html::submitButton('Create Batch', ['class' => 'btn btn-info','style'=>'float:left']);?>

                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                </div>
            </div>

            <div class="disbursement-details-index">

            </div>
            <?= Html::endForm(); ?>
        <?php } else { ?>
            <hr>
            <h3><!--Select Project First!--></h3>
        <?php } ?>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => "",// always need it for jquery plugin
        ]) ?>
        <?php Modal::end(); ?>
    </div>
</div>
