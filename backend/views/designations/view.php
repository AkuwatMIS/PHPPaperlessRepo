<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Designations */
?>
<div class="designations-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'desig_label',
            'code',
            'sorting',
            [
                'attribute'=>'network',
                'label'=>'Network',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->network == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'progress_report',
                'label'=>'Progress Report',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->progress_report == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'projects',
                'label'=>'Projects',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->projects == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'districts',
                'label'=>'Districts',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->districts == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'products',
                'label'=>'Products',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->products == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'analysis',
                'label'=>'Analysis',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->analysis == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'search_loan',
                'label'=>'Search Loan',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->search_loan == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'news',
                'label'=>'News',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->news == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'maps',
                'label'=>'Maps',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->maps == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'staff',
                'label'=>'Staff',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->staff == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'links',
                'label'=>'Links',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->links == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'filters',
                'label'=>'Filters',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->filters == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'housing',
                'label'=>'Housing',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->housing == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
            [
                'attribute'=>'audit',
                'label'=>'Audit',
                'format'=>'raw',
                'value'=>function ($data) {
                    if ($data->audit == 0) {
                        return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                    } else {
                        return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                    }
                }
            ],
        ],
    ]) ?>

</div>
