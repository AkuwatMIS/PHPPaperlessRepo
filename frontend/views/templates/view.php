<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Templates */
?>

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
        <h6 class="address-heading">
            Events List
        </h6>
        <div class="row">
                <div class="col-sm-12" style="margin-top: 3%">
                    <div class="card-grid-col">
                        <article class="card-typical">
                            <div class="card-typical-section" style="background-color: #00a8ff">
                                <div class="user-card-row">
                                    <div class="tbl-row">
                                        <div class="tbl-cell tbl-cell-photo">
                                            <a href="#">
                                                <img src="img/avatar-2-64.png" alt="">
                                            </a>
                                        </div>
                                        <div class="tbl-cell">
                                            <p  class="user-card-row-name"><a style="color: white" href="#"><?= $model->template_name.'('.$model->event->name.')' ?></a></p>
                                        </div>
                                        <div class="tbl-cell tbl-cell-status">
                                            <a href="#" ><span style="color: white"  class="font-icon font-icon-star"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-typical-section card-typical-content">
                                <header class="title"><a href="#"><?= $model->module ?> Module</a></header>

                            </div>
                            <div class="card-typical-section" style="background-color: lightgrey">
                                <div class="card-typical-linked"><a href="#">Active</a></div>
                                <a target="_blank" href="/templates/create?event_id=<?php echo $model->id?>" class="card-typical-likes">
                                    <i>Template</i>
                                </a>
                            </div>
                        </article><!--.card-typical-->
                    </div>
                </div>
        </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'event.name',
            'template_name',
            ['attribute'=>'template_text','format'=>'html'],
            'template_type',
            //'subject',
            //'email:email',
            //'send_to',
            //'is_active',
        ],
    ]) ?>

</div>
