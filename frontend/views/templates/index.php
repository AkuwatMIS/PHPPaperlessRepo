<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TemplatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <style>

            .tags {
                list-style: none;
                margin: 0;
                overflow: hidden;
                padding: 0;
            }

            .tags li {
                float: left;
            }

            .tag {
                background: #eee;
                border-radius: 3px 0 0 3px;
                color: #999;
                display: inline-block;
                height: 26px;
                line-height: 26px;
                padding: 0 20px 0 23px;
                position: relative;
                margin: 0 10px 10px 0;
                text-decoration: none;
                -webkit-transition: color 0.2s;
            }

            .tag::before {
                background: #fff;
                border-radius: 10px;
                box-shadow: inset 0 1px rgba(0, 0, 0, 0.25);
                content: '';
                height: 6px;
                left: 10px;
                position: absolute;
                width: 6px;
                top: 10px;
            }

            .tag::after {
                background: #fff;
                border-bottom: 13px solid transparent;
                border-left: 10px solid #eee;
                border-top: 13px solid transparent;
                content: '';
                position: absolute;
                right: 0;
                top: 0;
            }

            .tag:hover {
                background-color: #00a8ff;
                color: white;
            }

            .tag:hover::after {
                border-left-color: #00a8ff;
            }
            .btn-md {
                font-size: 15px;
                width: 160px;
                background-color: #5A738E;
            }

            .reset {
                font-size: 15px;
                width: 160px;
            }
            .radio input {
                position: relative;
                visibility: visible;
            }
            label {
                display: inline;
            }
        </style>
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
                <div class="row">
                    <div class="col-sm-6">
                        <h6 class="address-heading">
                            Templates
                        </h6>
                    </div>
                    <div class="col-sm-6">
                        <a target="_blank" href="/templates/create" class="btn  btn-success pull-right">
                            <i class="glyphicon glyphicon-plus"> Template</i>
                        </a>
                    </div>


                </div>

                <div class="row">
                    <?php foreach($dataProvider->getModels() as $model){ ?>
                        <div class="col-sm-6" style="margin-top: 3%">
                            <div class="card-grid-col">
                                <article class="card-typical">
                                    <div class="card-typical-section" style="background-color: darkorange">
                                        <div class="user-card-row">
                                            <div class="tbl-row">
                                                <div class="tbl-cell tbl-cell-photo">
                                                    <a href="#">
                                                        <img src="img/avatar-2-64.png" alt="">
                                                    </a>
                                                </div>
                                                <div class="tbl-cell">
                                                    <p  class="user-card-row-name"><a style="color: white" href="#"> <?= $model->template_name ?></a></p>
                                                </div>
                                                <div class="tbl-cell tbl-cell-status">
                                                    <a href="#" ><span style="color: white"  class="font-icon font-icon-star"></span></a>
                                                    <?=\yii\helpers\Html::a('<span style="color: white"  class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['target' => '_blank'], ['title' => 'Update']);?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-typical-section card-typical-content">
                                        <header style="text-align: center" class="title"><a href="#"><?= $model->module ?> Module</a></header>
                                        <hr>
                                        <p style="text-align: center"><?= $model->template_text ?></p>
                                        <!--<ul class="tags">
                                            <?php /*$i=1; foreach (\common\components\Helpers\ListHelper::getLists($model->module.'_placeholders') as $key=>$value){ */?>
                                                <li><p id="tag-<?/*= $i;*/?>" href="#" class="tag"><?/*= $value*/?></p></li>
                                                <?php /*$i++;}*/?>
                                        </ul>-->
                                    </div>
                                    <div class="card-typical-section" style="background-color:#d3af37  ">
                                        <!--<div class="card-typical-linked"><i style="color: green" class="fa fa-adn"> Active</i><a href="#"></a></div>-->
                                        <div class="card-typical-likes"><i style="color: white" class="fa fa-adn"> Active</i><a href="#"></a></div>
                                        <!--<a target="_blank" href="/templates/create?event_id=<?php /*echo $model->id*/?>" class="card-typical-likes">
                                            <i>Template</i>
                                        </a>-->
                                    </div>
                                </article><!--.card-typical-->
                            </div>
                        </div>
                    <?php } ?>
                </div>
        <!--<h4 class="address-heading">
            Templates List
        </h4>
        <div class="events-index">
            <div id="ajaxCrudDatatable">
                <?/*= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => require(__DIR__ . '/_columns.php'),
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                    'pager' => [
                        'options' => ['class' => 'pagination'],   // set clas name used in ui list of pagination
                        'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
                        'nextPageLabel' => 'Next',   // Set the label for the "next" page button
                        'firstPageLabel' => 'First',   // Set the label for the "first" page button
                        'lastPageLabel' => 'Last',    // Set the label for the "last" page button
                        'nextPageCssClass' => 'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass' => 'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass' => 'last',    // Set CSS class for the "last" page button
                        'maxButtonCount' => 10,    // Set maximum number of page buttons that can be displayed
                    ],
                ]); */?>
            </div>
        </div>
        <?php /*Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => "",// always need it for jquery plugin
        ]) */?>
        --><?php /*Modal::end(); */?>

    </div>
            <?php /*echo \yii\widgets\LinkPager::widget(['pagination' => $pagination]);*/?>
</div>