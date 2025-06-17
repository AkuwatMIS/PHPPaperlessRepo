<?php

use yii\widgets\DetailView;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Branch</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="row">
        <div class="col-lg-3">
            <section class="box-typical">
                <header class="box-typical-header-sm bordered text-center"><h6><b>Demographic Hierarchy</b></h6>
                </header>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Country</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->country->name) ? $model->country->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Province</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->province->name) ? $model->province->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Division</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->division->name) ? $model->division->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>District</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->district->name) ? $model->district->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>City</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->city->name) ? $model->city->name : '-' ?>
                        </div>
                    </div>
                </div>
                <header class="box-typical-header-sm bordered text-center"><h6><b>Credit Structure Information</b></h6>
                </header>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Credit Division</b>
                        </div>
                        <div class="tbl-cell">
                            Division 1
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Region</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->region->name) ? $model->region->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Area</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->area->name) ? $model->area->name : '-' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Branch</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($model->name) ? $model->name : '-' ?>
                        </div>
                    </div>
                </div>
                <!--<div class="profile-card">
                    <div class="profile-card-name">Credit Division</div>
                    <div class="profile-card-status">Division 1</div>
                </div>
                <div class="profile-card">
                    <div class="profile-card-name">Region</div>
                    <div class="profile-card-status"><? /*= isset($model->region->name) ? $model->region->name : '-' */ ?></div>
                </div>
                <div class="profile-card">
                    <div class="profile-card-name">Area</div>
                    <div class="profile-card-status"><? /*= isset($model->area->name) ? $model->area->name : '-' */ ?></div>
                </div>
                <div class="profile-card">
                    <div class="profile-card-name">Branch</div>
                    <div class="profile-card-status"><? /*= isset($model->name) ? $model->name : '-' */ ?></div>
                </div>-->
                <?php foreach ($model->teams as $team) { ?>
                    <div class="profile-card">
                        <div class="profile-card-name"><?php echo $team->name ?></div>
                        <div class="profile-statistic tbl">
                            <div class="tbl-row">
                                <?php foreach ($team->fields as $field) { ?>
                                    <div class="tbl-cell">
                                        <?php echo $field->name ?>
                                        <hr>
                                        <p><?php echo isset($field->userStructureMapping->user->username) ? $field->userStructureMapping->user->username : 'Loan Officer Not Assigned' ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <br>
            </section><!--.box-typical-->
        </div><!--.col- -->
        <div class="col-xl-6 col-lg-6">
            <section class="box-typical">
                <header class="box-typical-header-sm bordered"><b>Branch Details</b>
                    (<?= isset($model->name) ? $model->name : '-' ?> /
                    Code: <?= isset($model->code) ? $model->code : '-' ?>)
                </header>
                <div class="row">
                    <div class="col-md-6">
                        </br>
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-build"></i>
                                <b>Branch Information</b>
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Name</b>
                                    : <?= isset($model->name) ? $model->name : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Code</b>
                                    : <?= isset($model->code) ? $model->code : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Opening Date</b>
                                    : <?= isset($model->opening_date) ? date('d M Y', $model->opening_date) : 'Not Set'; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-6">
                        <br>
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="glyphicon glyphicon-phone"></i>
                                <b>Address Information</b>
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Address</b>
                                    : <?= isset($model->address) ? $model->address : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Village</b>
                                    : <?= isset($model->village) ? $model->village : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>UC</b>
                                    : <?= isset($model->uc) ? $model->uc : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Contact No</b>
                                    : <?= isset($model->mobile) ? $model->mobile : 'Not Set'; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <!--<? /*= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'opening_date',
                                'label' => 'Opening Date',
                                'value' => function ($data) {
                                    return date('d M Y',$data->opening_date);
                                },
                            ],
                            'address',
                            'mobile',
                            'uc',
                            'village',
                            'description:ntext',
                        ],
                    ]) */ ?>-->
                </div>

                <div>
                    <?php
                    (isset($model->latitude) && isset($model->latitude)) ? $model->coordinates = $model->latitude . ',' . $model->longitude : $model->coordinates = '33.5753184,73.14307400000007';
                    $form = ActiveForm::begin();
                    echo $form->field($model, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker', [
                        'key' => 'AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI',   // optional , Your can also put your google map api key
                        'valueTemplate' => '{latitude},{longitude}', // Optional , this is default result format
                        'options' => [
                            'style' => 'width: 100%; height: 300px',  // map canvas width and height
                        ],
                        'enableSearchBox' => true, // Optional , default is true
                        'searchBoxOptions' => [ // searchBox html attributes
                            'style' => 'width: 500px;', // Optional , default width and height defined in css coordinates-picker.css
                        ],
                        'mapOptions' => [
                            // set google map optinos
                            'rotateControl' => true,
                            'scaleControl' => false,
                            'streetViewControl' => true,
                            'mapTypeId' => new JsExpression('google.maps.MapTypeId.ROADMAP'),
                            'heading' => 90,
                            'tilt' => 45,

                            'mapTypeControl' => true,
                            'mapTypeControlOptions' => [
                                'style' => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
                                'position' => new JsExpression('google.maps.ControlPosition.TOP_CENTER'),
                            ]
                        ],
                        'clientOptions' => [
                            'radius' => 50,
                            'addressFormat' => 'street_number',
                            'inputBinding' => [
                                'latitudeInput' => new JsExpression("$('#us2-lat')"),
                                'longitudeInput' => new JsExpression("$('#us2-lon')"),
                                'locationNameInput' => new JsExpression("$('#us2-address')")
                            ],
                            'autoComplete' => true,
                        ]
                    ])->label(false);

                    ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </section>
        </div>
        <div class="col-lg-3">
            <section class="box-typical">
                <header class="box-typical-header-sm bordered"><h6><b>Projects</b></h6></header>
                <div class="box-typical-inner">
                    <ul class="profile-links-list">
                        <?php $i = 1; ?>
                        <?php foreach ($array['branch_projects'] as $project) { ?>
                            <li class="nowrap"><?php echo '<b>' . $i . ' </b>- ' . $project->project->name ?></li>
                            <?php $i++;
                        } ?>
                    </ul>
                </div>
            </section>
            <section class="box-typical">
                <header class="box-typical-header-sm bordered"><h6><b>Accounts</b></h6></header>
                <div class="box-typical-inner">
                    <ul class="profile-links-list">
                        <?php $i = 1; ?>
                        <?php foreach ($array['branch_accounts'] as $account) { ?>
                            <li class="nowrap"><?php echo '<b>' . $i . ' </b>- ' . $account->account->acc_no ?></li>
                            <?php $i++;
                        } ?>
                    </ul>
                </div>
            </section>
        </div><!--.col- -->
    </div><!--.row-->

    <div class="table-responsive">
        <table class="table table-bordered">
            <h4>Progress Report</h4>

            <thead>
            <tr>
                <th>Total Loans</th>
                <th>Male Loans</th>
                <th>Female Loans</th>
                <th>Active Loans</th>
                <th>Cum. Disb</th>
                <th>Cum. Due</th>
                <th>Cum. Recv</th>
                <th>Not Yet Due</th>
                <th>OLP</th>

            </tr>
            </thead>
            <tbody>
            <?php $count = 1;
            foreach ($array['progress'] as $configs) { ?>
                <tr>
                    <td><?= isset($array['progress'][0]['no_of_loans']) ? number_format($$array['progress'][0]['no_of_loans']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['family_loans']) ? number_format($array['progress'][0]['family_loans']) : '0' ?></td>
                    <td><?= isset($array['progress']['female_loans']) ? number_format($array['progress'][0]['female_loans']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['active_loans']) ? number_format($array['progress'][0]['active_loans']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['cum_disb']) ? number_format($array['progress'][0]['cum_disb']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['cum_due']) ? number_format($array['progress'][0]['cum_due']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['cum_recv']) ? number_format($array['progress'][0]['cum_recv']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['not_yet_due']) ? number_format($array['progress'][0]['not_yet_due']) : '0' ?></td>
                    <td><?= isset($array['progress'][0]['olp_amount']) ? number_format($array['progress'][0]['olp_amount']) : '0' ?></td>

                </tr>
                <?php $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <!--<div class="table-responsive">
        <table class="table table-bordered">
            <h4>Configuration</h4>

            <thead>
            <tr>
                <th>#</th>
                <th>Group</th>
                <th>Priority</th>
                <th>Key</th>
                <th>Value</th>
                <th>Parent Type</th>
                <th>Parent_Id</th>
                <th>Project Id</th>

            </tr>
            </thead>
            <tbody>
            <?php /*$count = 1;
            foreach ($array['configurations'] as $configs) { */?>
                <tr>
                    <td><?/*= $count */?></td>
                    <td><?/*= $configs['group'] */?></td>
                    <td><?/*= $configs['priority'] */?></td>
                    <td><?/*= $configs['key'] */?></td>
                    <td><?/*= $configs['value'] */?></td>
                    <td><?/*= $configs['parent_type'] */?></td>
                    <td><?/*= $configs['parent_id'] */?></td>
                    <td><?/*= $configs['project_id'] */?></td>

                </tr>
                <?php /*$count++;
            } */?>
            </tbody>
        </table>
    </div>-->
</div><!--.container-fluid-->


