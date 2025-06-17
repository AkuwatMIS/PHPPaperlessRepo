<?php

use dimmitri\grid\ExpandRowColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmergencyLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emergency Loans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
           Emergency Loans
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'projects_name' =>$projects_name,
            'provinces' =>$provinces,
            'districts' =>$districts,
            'cities' =>$cities,
        ]); ?>

        <div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'province_id',
                'value'=>'loan.branch.province.name',
                'label' => 'Province',
                'filter' => $provinces,
            ],
            [
                'attribute' => 'district_id',
                'value'=>'loan.branch.district.name',
                'label' => 'District',
                'filter' => $districts,
            ],
            [
                'attribute' => 'city_id',
                'value'=>'loan.branch.city.name',
                'label' => 'City',
                'filter' => $cities,
            ],
            [
                'attribute' => 'member_name',
                'value'=>'loan.application.member.full_name',
                'label' => 'Name'
            ],
            [
                'attribute'=>'member_parentage',
                'value'=>'loan.application.member.parentage',
                'label' => 'Parentage'
            ],
            [
                'attribute'=>'member_cnic',
                'value'=>'loan.application.member.cnic',
                'label' => 'CNIC'
            ],

            [
                'attribute'=>'sanction_no',
                'value'=> 'loan.sanction_no',
                'label' => 'Sanction No'
            ],
            [
                'attribute'=>'date_disbursed',
                //'value'=> 'loan.date_disbursed',
                'value'=>function ($model, $key, $index) {
                    if($model->loan->date_disbursed != 0) {
                        return \common\components\Helpers\StringHelper::dateFormatter($model->loan->date_disbursed);
                    }
                },
                'label' => 'Date Disbursed'
            ],
            [
                'attribute'=>'donated_date',
                //'value'=> 'donated_date',
                'value'=>function ($model, $key, $index) {
                    if($model->donated_date != 0) {
                        //return \common\components\Helpers\StringHelper::dateFormatter($model->donated_date);
                        return date('d M Y', strtotime($model->donated_date));
                    }
                },
                'label' => 'Donated Date',
            ],
            [
                'attribute'=>'status',
                //'value'=> 'status',
                'value'=>function ($model, $key, $index) {
                    if($model->status==0) {
                        return "Not Donated";
                    }
                    else return "Donated";
                },
                'label' => 'Status',
                'filter' => [0 => "Not Donated", 1=> "Donated"],
            ],

            [
                'attribute'=>'project_id',
                'value'=> 'loan.project.name',
                'label' => 'Project',
                'filter'=> $projects_name
            ],

           // ['class' => 'yii\grid\ActionColumn'],
        ],
        'footerRowOptions' => ['style' => 'font-weight:bold;'],
        'showFooter' => true,
        'summary' => '
         Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
         <div class="dropdown pull-right">
            <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <i class="glyphicon glyphicon-export"></i>
            <span class="caret"></span></button>
                        
             <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation" >
                   <form action="/emergency-loans/emergency-loans-city-wise">
                     <input type="hidden" name="EmergencyLoansSearch[full_name]" value="' . $searchModel->member_name . '">
                     <input type="hidden" name="EmergencyLoansSearch[parentage]" value="' . $searchModel->member_parentage. '">
                     <input type="hidden" name="EmergencyLoansSearch[cnic]" value="' . $searchModel->member_cnic . '">
                     <input type="hidden" name="EmergencyLoansSearch[sanction_no]" value="' . $searchModel->sanction_no . '">
                     <input type="hidden" name="EmergencyLoansSearch[donated_date]" value="' . $searchModel->donated_date . '">
                     <input type="hidden" name="EmergencyLoansSearch[status]" value="' . $searchModel->status . '">
                     <input type="hidden" name="EmergencyLoansSearch[province]" value="' . $searchModel->province_id. '">
                     <input type="hidden" name="EmergencyLoansSearch[district]" value="' . $searchModel->district_id. '">
                     <input type="hidden" name="EmergencyLoansSearch[city]" value="' . $searchModel->city_id. '">
                     
                    <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                        role="menuitem" tabindex="-1"><i
                            class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                    </button>
                </form>
                </li>
            </ul>
         </div>
               ',
    ]); ?>
</div>
