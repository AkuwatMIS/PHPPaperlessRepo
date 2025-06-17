<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $model->member->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Verifications', 'url' => ['verification']];
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $model common\models\BusinessAppraisal */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="container-fluid">
    <!--<header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><b>Details</b></h4>
                </div>
            </div>
        </div>
    </header>-->
    <div class="box-typical box-typical-padding">
        <section class="widget widget-accordion" id="accordion" aria-multiselectable="false">
            <?php if(!empty($model->socialAppraisal)){?>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingThree">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                       aria-expanded="false" aria-controls="collapseThree">
                        Social Appraisal
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "social_appraisal_check-tick" ?>></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingThree">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->socialAppraisal,
                                            'attributes' => [
                                                'poverty_index',
                                                'house_ownership',
                                                [
                                                    'attribute'=>'house_rent_amount',
                                                    'value'=>function($data){
                                                       return isset($data->house_rent_amount)?number_format($data->house_rent_amount):'';
                                                    }
                                                ],
                                                'land_size',
                                                [
                                                    'attribute'=>'total_family_members',
                                                    'value'=>function($data){
                                                        return isset($data->total_family_members)?number_format($data->total_family_members):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'ladies',
                                                    'value'=>function($data){
                                                        return isset($data->ladies)?number_format($data->ladies):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'gents',
                                                    'value'=>function($data){
                                                        return isset($data->gents)?number_format($data->gents):'';
                                                    }
                                                ],
                                                'source_of_income',
                                                [
                                                    'attribute'=>'total_household_income',
                                                    'value'=>function($data){
                                                        return isset($data->total_household_income)?number_format($data->total_household_income):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'educational_expenses',
                                                    'value'=>function($data){
                                                        return isset($data->educational_expenses)?number_format($data->educational_expenses):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'medical_expenses',
                                                    'value'=>function($data){
                                                        return isset($data->medical_expenses)?number_format($data->medical_expenses):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'kitchen_expenses',
                                                    'value'=>function($data){
                                                        return isset($data->kitchen_expenses)?number_format($data->kitchen_expenses):'';
                                                    }
                                                ],

                                            ]]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->socialAppraisal,
                                            'attributes' => [
                                                'monthly_savings',
                                                [
                                                    'attribute'=>'amount',
                                                    'value'=>function($data){
                                                        return isset($data->amount)?number_format($data->amount):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'other_expenses',
                                                    'value'=>function($data){
                                                        return isset($data->other_expenses)?number_format($data->other_expenses):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'total_expenses',
                                                    'value'=>function($data){
                                                        return isset($data->total_expenses)?number_format($data->total_expenses):'';
                                                    }
                                                ],
                                                'other_loan',
                                                [
                                                    'attribute'=>'loan_amount',
                                                    'value'=>function($data){
                                                        return isset($data->loan_amount)?number_format($data->loan_amount):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'business_income',
                                                    'value'=>function($data){
                                                        return isset($data->business_income)?number_format($data->business_income):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'job_income',
                                                    'value'=>function($data){
                                                        return isset($data->job_income)?number_format($data->job_income):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'house_rent_income',
                                                    'value'=>function($data){
                                                        return isset($data->house_rent_income)?number_format($data->house_rent_income):'';
                                                    }
                                                ],
                                                [
                                                    'attribute'=>'other_income',
                                                    'value'=>function($data){
                                                        return isset($data->other_income)?number_format($data->other_income):'';
                                                    }
                                                ],
                                                'economic_dealings',
                                                'social_behaviour'
                                            ]]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </article>
            <?php }?>
            <?php if(!empty($model->businessAppraisal)){?>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingFour">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour"
                       aria-expanded="false" aria-controls="collapseFour">
                        Business Appraisal
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "business_appraisal_check-tick" ?>></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseFour" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingFour">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->businessAppraisal,
                                            'attributes' => [
                                                //'business_type',
                                                //'business',
                                                'place_of_business',
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Business
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->fixed_business_assets) ? $model->businessAppraisal->fixed_business_assets : ''
                                                        ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->fixed_business_assets_amount) ? number_format($model->businessAppraisal->fixed_business_assets_amount) : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Fixed
                                                        Business
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->business_expenses) ? $model->businessAppraisal->business_expenses : '';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->business_expenses_amount) ? number_format($model->businessAppraisal->business_expenses_amount) : '';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>New
                                                        Required
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->new_required_assets) ? $model->businessAppraisal->new_required_assets : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->new_required_assets_amount) ? number_format($model->businessAppraisal->new_required_assets_amount) : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Running
                                                        Capital</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->running_capital) ? $model->businessAppraisal->running_capital : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->running_capital_amount) ? number_format($model->businessAppraisal->running_capital_amount) : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <?php }?>
            <?php if(!empty($model->housingAppraisal)){?>
                <article class="panel">
                    <div class="panel-heading" role="tab" id="headingFive">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive"
                           aria-expanded="false" aria-controls="collapseFive">
                            Housing Appraisal
                            <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                                  id= <?= "social_appraisal_check-tick" ?>></span>
                            <i class="font-icon font-icon-arrow-down"></i>
                        </a>
                    </div>
                    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="headingFive">
                        <div class="panel-collapse-in">
                            <div class="user-card-row">
                                <div class="tbl-row">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= \yii\widgets\DetailView::widget([
                                                'model' => $model->housingAppraisal,
                                                'attributes' => [
                                                    'property_type',
                                                    'ownership',
                                                    'land_area',
                                                    'residential_area',
                                                    //'living_duration',
                                                    //'duration_type',
                                                    [
                                                        'attribute'=>'no_of_rooms',
                                                        'value'=>function($data){
                                                            return isset($data->no_of_rooms)?number_format($data->no_of_rooms):'';
                                                        }
                                                    ],
                                                ]]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= \yii\widgets\DetailView::widget([
                                                'model' => $model->housingAppraisal,
                                                'attributes' => [
                                                    [
                                                        'attribute'=>'no_of_kitchens',
                                                        'value'=>function($data){
                                                            return isset($data->no_of_kitchens)?number_format($data->no_of_kitchens):'';
                                                        }
                                                    ],
                                                    [
                                                        'attribute'=>'no_of_toilets',
                                                        'value'=>function($data){
                                                            return isset($data->no_of_toilets)?number_format($data->no_of_toilets):'';
                                                        }
                                                    ],

                                                    [
                                                        'attribute'=>'purchase_price',
                                                        'value'=>function($data){
                                                            return isset($data->purchase_price)?number_format($data->purchase_price):'';
                                                        }
                                                    ],
                                                    [
                                                        'attribute'=>'current_price',
                                                        'value'=>function($data){
                                                            return isset($data->current_price)?number_format($data->current_price):'';
                                                        }
                                                    ],
                                                    //'address',
                                                    //'estimated_completion_time',
                                                    'status',
                                                ]]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </article>
            <?php }?>
            <?php if(in_array($model->project_id,\common\components\Helpers\StructureHelper::trancheProjects())){?>
                <article class="panel">
                    <div class="panel-heading" role="tab" id="headingSix">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix"
                           aria-expanded="false" aria-controls="collapseFive">
                            Civil Engineer
                            <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                                  id= <?= "social_appraisal_check-tick" ?>></span>
                            <i class="font-icon font-icon-arrow-down"></i>
                        </a>
                    </div>
                    <div id="collapseSix" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="headingSix">
                        <div class="panel-collapse-in">
                            <div class="user-card-row">
                                <div class="tbl-row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= \yii\widgets\DetailView::widget([
                                                'model' => $model,
                                                'attributes' => [
                                                    [
                                                        'attribute'=>'recommended_amount',
                                                        'value'=>function($data){
                                                            return isset($data->recommended_amount)?number_format($data->recommended_amount):'';
                                                        }
                                                    ],
                                                ]]); ?>
                                        </div>
                                        <br><br>
                                        <br><br>
                                        <div class="col-md-6">
                                            <?php echo \yii\helpers\Html::a('Visits Detail', ['applications/visit-details', 'id' => $model->id], ['target'=>'_blank','class'=>'pull-right'], ['title' => 'Visit History'],['class'=>' pull-right']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </article>
            <?php }?>
        </section>
    </div>
</div>






