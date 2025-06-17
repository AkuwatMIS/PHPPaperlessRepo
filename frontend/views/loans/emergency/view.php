<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$limit=($dataProvider->count);

/*$this->title = 'Home';*/
$this->params['breadcrumbs'][] = $this->title;
$loan=new \common\models\Loans();
$js = '';
$js .= "
$('body').on('beforeSubmit', 'form.UpdateChequeNo', function () {
     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
    
   if (confirm(\"Do you want to add this borrower for Emergency Loan?\")) {
      var loan_id = serial_no;
     //alert(loan_id);
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     // submit form
     $.ajax({
          url: '/loans/add-emergency?id='+serial_no,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    $('#save-button-'+serial_no).prop('disabled', true);
                    $('#'+serial_no+'-tick').show();
                }else{
                 var c='';
                 $('#status-'+serial_no).addClass('error');
                  jQuery.each(obj.errors, function(index, item) {
                    if(c!=''){
                      c += ',';
                    }
                        c +=item;
                  });
                
                 $('#status-'+serial_no).text(c);
                 $('#status-'+serial_no).show();
 
                }
          }
     });
     return false;
  } else {
   
  } 
   
});

window.setTimeout(function() {
    $(\".sttus\").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 500);
";
$this->registerJs($js);
?>


<?php

echo $this->render('_search', [
    'searchModel' => $searchModel,
    'types' => $types
]);
if (isset($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) {
        ?>
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">

                <div class="table-responsive">
                    <!--<?/*= GridView::widget([
                        'dataProvider' => $dataProvider,
                       // 'filterModel' => $searchModel,
                        'columns' => require(__DIR__ . '/_columns.php'),
                        'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                    ]); */?>-->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <h4>Loans List</h4>

                            <tr>
                                <th>#</th>
                                <th>Member Name</th>
                                <th>Member Cnic</th>
                                <th>Application No</th>
                                <th>Sanction No</th>
                                <th>Loan Amount</th>
                                <th>Inst Amount</th>
                                <th>Inst Months</th>
                                <th>Inst Type</th>
                                <th>Date Disbursed</th>
                                <th>Group No</th>
                                <th>Project</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $count=1; foreach($dataProvider->getModels()  as $l){
                                ?>
                                <?php $form = \yii\widgets\ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $l['id'], 'options' => [
                                    'class' => 'UpdateChequeNo'
                                ]
                                ]);
                                ?>
                                <?= $form->field($loan, "[{$j}]id")->hiddenInput(['name' => 'Loans[id]','value'=>$l['id']])->label(false) ?>

                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $l['member_name'] ?></td>
                                    <td><?= $l['member_cnic']?></td>
                                    <td><?= $l['application_no'] ?></td>
                                    <td><?= $l['sanction_no'] ?></td>
                                    <td><?= number_format($l['loan_amount']) ?></td>
                                    <td><?= number_format($l['inst_amnt']) ?></td>
                                    <td><?= number_format($l['inst_months']) ?></td>
                                    <td><?= $l['inst_type'] ?></td>
                                    <td><?= date('d M Y',$l['date_disbursed']);?></td>
                                    <td><?= $l['grp_no'] ?></td>
                                    <td><?= $l['project_name'] ?></td>
                                    <td>
                                        <?php if(empty(\common\models\EmergencyLoans::find()->where(['loan_id'=>$l['id']])->one())){ ?>
                                        <?= Html::submitButton('Add', ['id' => 'save-button-' . $l['id'], 'class' => 'btn btn-success btn-rounded btn-sm disb']) ?>
                                        <span class="glyphicon glyphicon-ok" style="color:green;display: none;"
                                              id= <?= $l['id'] . "-tick" ?></span>
                                        <div id="status-<?php echo $j; ?>" style="display: none;" class="status"></div>
                                        <?php }else{ ?>
                                            <p>Already Added</p>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php $form->end(); ?>
                                <?php $count++; } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    <?php } else {
        ?>
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">

                <div class="table-responsive">
                    <h3>No record found</h3>
                </div>
            </div>
        </div>

    <?php }
}
Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end();
?>


