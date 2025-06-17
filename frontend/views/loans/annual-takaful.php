<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use \common\models\Operations;
use \common\models\Schedules;
$this->title = 'Pending Takaful ';
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4>Search Pending Takaful </h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="disbursements-form">
            <div class="disbursements-form">
                <?php $form = ActiveForm::begin(['id' => 'annual-takaful','method'=>'get']); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <?= $form->field($loans_search, 'branch_id')->dropDownList($branches,['prompt'=>'Select Branch'])->label('Select Branch') ?>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="col-md-4">
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div><div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <?php if (!empty($model)) { ?>
                    <div class="box-typical box-typical-padding">
                        <div>
                            <h4>Pending Takaful</h4>
                        </div>
                        <table id="table-edit" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Name</th>
                                <th>Parentage</th>
                                <th>CNIC</th>
                                <th>Sanction No</th>
                                <th>OLP</th>
                                <th>Disb Date</th>
                                <th>Takaful Date</th>
                                <th>Takaful Receipt</th>
                                <th>Takaful Amount</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;
                            foreach ($model->getModels() as $key=>$takaf) {  ?>
                                <?php
                                $loan=$takaf->loan->application_id;
                                ?>
                                <tr>
                                    <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i, 'options' => [
                                        'class' => 'formInstantDisb'
                                    ]
                                    ]);
                                    ?>
                                    <td><?= $i+1 ?></td>
                                    <td><?= isset($takaf->loan->application->member->full_name)?$takaf->loan->application->member->full_name:'Not Set' ?></td>
                                    <td><?= isset($takaf->loan->application->member->parentage)?$takaf->loan->application->member->parentage:'Not Set' ?></td>
                                    <td><?= isset($takaf->loan->application->member->cnic)?$takaf->loan->application->member->cnic:'Not Set' ?></td>
                                    <td><?= isset($takaf->loan->sanction_no)?$takaf->loan->sanction_no:'Not Set' ?></td>
                                    <td><?= number_format($takaf->olp)?></td>
                                    <?= $form->field($takaf, "[{$i}]id")->hiddenInput(['class' => 'id','value'=>$takaf->loan->id])->label(false) ?>
                                    <td><?= isset($takaf->disb_date)?date('d-M-Y',$takaf->disb_date):'Not Set' ?></td>
                                    <td>
                                        <?php echo \yii\jui\DatePicker::widget([
                                            'name' => 'Operations[receive_date]',
                                            'value' => date('d-M-Y'),
                                            'options' => ['placeholder' => 'Select date',
                                                'class'=>'form-control input-sm takaf -'.$i.'-receive-date',
                                                'type' => \kartik\date\DatePicker::TYPE_INPUT,
                                                'format' => 'dd-M-yyyy',
                                                'todayHighlight' => true,
                                            ],
                                        ]);?>
                                    </td>
                                    <td><input class='form-control input-sm takaf -<?php echo $i?>-receipt-no' type="text" id="receipt_no"
                                               name="Operations[receipt_no]" required="required"></td>
                                    <td><input class="form-control input-sm takaf -<?php echo $i?>-credit" type="text" id="credit" name="Operations[credit]"  value=<?php echo $takaf->takaful_amnt ?>></td>
                                    <input type="hidden" id="application_id" value="<?php echo $takaf->loan->application_id ?>"
                                           name="Operations[application_id]">
                                    <input type="hidden" id="operation_type_id" value="2"
                                           name="Operations[operation_type_id]">
                                    <input type="hidden" id="region_id" value="<?php echo $takaf->region_id ?>"
                                           name="Operations[region_id]">
                                    <input type="hidden" id="area_id" value="<?php echo $takaf->area_id ?>"
                                           name="Operations[area_id]">
                                    <input type="hidden" id="branch_id" value="<?php echo $takaf->branch_id ?>"
                                           name="Operations[branch_id]">
                                    <input type="hidden" id="team_id" value="<?php echo $takaf->loan->team_id ?>"
                                           name="Operations[team_id]">
                                    <input type="hidden" id="field_id" value="<?php echo $takaf->loan->field_id ?>"
                                           name="Operations[field_id]">
                                    <input type="hidden" id="project_id" value="<?php echo $takaf->loan->project_id ?>"
                                           name="Operations[project_id]">
                                    <input type="hidden" id="loan_id" value="<?php echo $takaf->loan->id ?>"
                                           name="Operations[loan_id]">
                                    <td>
                                        <?= Html::submitButton('save', ['id'=>'save-button-' . $i,'class' => 'btn btn-success btn-sm disb']) ?>
                                        <div id="status-<?php echo $i; ?>" style="display: none;" class="status"></div>
                                    </td>
                                    <?php $form->end(); ?>

                                </tr>
                            <?php } ?>
                            <?php  ?>
                            <?php $i++;
                            }?>
                            </tbody>
                        </table>
                    </div>
                    <?php  ?>
                </div>
            </div>
        </div>
    </header>
</div>