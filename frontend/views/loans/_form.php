<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
/* @var $form yii\widgets\ActiveForm */
$js = "
function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}
function calc_summary(){
var loan_amount=$(\"#loans-loan_amount\").val();
var inst_months=$(\"#loans-inst_months\").val();

if(loan_amount && inst_months){
                  //var charges_percentage = \"" . common\components\Helpers\LoanHelper::getProjectChargesFromId($application->project_id) . "\";
                  //var years=Math.ceil(inst_months/12);
                  //var charges=(loan_amount*charges_percentage*years)/100;
                  //var chargess=(parseInt(charges)/parseInt(inst_months));
                  //var total_amount=parseInt(loan_amount)+parseInt(charges);
                  //var inst_amnt = Math.ceil((total_amount/inst_months) / 100) *100;
                  
                  //$('.alert-success').show();
                  /*$(\"#total_amount\").text(formatNumber(total_amount));
                  $(\"#total_percentage\").text((charges_percentage*years));
                  $(\"#total_charges\").text(formatNumber(charges));
                  $(\"#inst_amount\").text(formatNumber(inst_amnt));*/
                  
                  //$(\"#total_amount\").text(formatNumber(total_amount));
                  //$(\"#total_percentage\").text((charges_percentage)+'%');
                  //$(\"#total_charges\").text(formatNumber(Math.round(chargess)));
                  //$(\"#inst_amount\").text(formatNumber(inst_amnt));
                  
               }
}
function calc_inst(){
    //alert($(\"#loans-project_id\").val());
    var project_id = $(\"#loans-project_id\").val();
    var product_id = $(\"#loans-product_id\").val();
    //var activity_id = $(\"#loans-activity_id\").val();
    var project_code = \"" . common\components\Helpers\LoanHelper::getProjectCodeFromId($application->project_id) . "\";
    var activity_name = \"" . common\components\Helpers\LoanHelper::getActivityNameFromId($application->activity_id) . "\";
    var charges_percentage = \"" . common\components\Helpers\LoanHelper::getProjectChargesFromId($application->project_id) . "\";

    $(\"#loans-inst_amnt\").val(0);
    $(\"#loans-service_charges\").val(0);
    var loan_amount = $(\"#loans-loan_amount\").val();
    if(project_code == \"gb\" && activity_name == \"Agriculture inputs\"){
        var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 20000){
                 //alert(\"here\");
                     var totalmonths = 12;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 20000 && loan_amount <= 30000){
                     var inst_amnt = 2000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 30000 && loan_amount <= 50000){
                     var totalmonths = 15;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 50000 && loan_amount <= 100000){
                     var totalmonths = 20;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 100000){
                     var totalmonths = 24;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
                 
    } else if(project_code == \"KP-ELS\"){
                 var totalmonths = $(\"#loans-inst_months\").val();
                 var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                 var LoanInstType = \"Monthly\";
                 $(\"#loans-inst_months\").val(totalmonths);
                 $(\"#loans-inst_amnt\").val(inst_amnt);
                 $(\"#loans-inst_type\").val(LoanInstType);
    } else if(project_code == \"PSIC\" && activity_name == \"Agriculture inputs\"){
        var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 35000){
                 //alert(\"here\");
                     var totalmonths = 20;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 35000 && loan_amount <= 50000){
                     var totalmonths = 24;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
    } else if(project_code == \"Ehsaas Naujawan Program\"){
                    var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 100000){
                 //alert(\"here\");
                     var totalmonths = 18;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 100000 && loan_amount <= 200000){
                     var totalmonths = 24;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 200000 ){
                     var totalmonths = 36;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
                 
    } else if(project_code == \"PM-YBLS\"){
         var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 50000){
                 //alert(\"here\");
                     var inst_amnt = 3000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 50001 && loan_amount <= 75000){
                    var inst_amnt = 3500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 75001 && loan_amount <= 100000){
                     var inst_amnt = 4000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 100001 && loan_amount <= 125000){
                    var inst_amnt = 4500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 125001 && loan_amount <= 150000){
                    var inst_amnt = 5000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 150001 && loan_amount <= 175000){
                    var inst_amnt = 5500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 175001 && loan_amount <= 200000){
                    var inst_amnt = 6000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                }else if(loan_amount > 200001 && loan_amount <= 500000){
                     var totalmonths = 36;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
                 
    } else if(project_code == \"PM-ALS\" && product_id==14){
         var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 50000){
                 //alert(\"here\");
                     var inst_amnt = 3000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 50001 && loan_amount <= 75000){
                    var inst_amnt = 3500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 75001 && loan_amount <= 100000){
                     var inst_amnt = 4000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 100001 && loan_amount <= 125000){
                    var inst_amnt = 4500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 125001 && loan_amount <= 150000){
                    var inst_amnt = 5000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 150001 && loan_amount <= 175000){
                    var inst_amnt = 5500;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 175001 && loan_amount <= 200000){
                    var inst_amnt = 6000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                }else if(loan_amount > 200001 && loan_amount <= 500000){
                     var totalmonths = 36;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
                 
    } else if(project_id == \"98\" || project_id == \"109\" || project_id == \"114\" || project_id == \"110\" || project_id == \"52\" || project_id == \"103\" || project_id==61 || project_id==62 || project_id==64 || project_id==67 || project_id==77 || project_id==90 || project_id==97 || project_id==113 || project_id==96 || project_id==100 || project_id==101 || project_id==118 || project_id==119 || project_id==120 || project_id==126 || project_id==127 || project_id==24 || project_id==129 || project_id==130 || project_id==131 || project_id==132 || project_id==136 || project_id==135 || project_id==137 || project_id==138 || project_id==141 || project_id==35 || project_id==143 || project_id==145){
            
            var LoanInstType = 'Monthly';
             $('#loans-inst_type').val(LoanInstType);
             var totalmonths = $(\"#loans-inst_months\").val();
             if(totalmonths){
                  if(project_id == \"119\" && parseInt(loan_amount) <= 30000){
                    var years=(totalmonths/12);
                    var total_amount=parseInt(loan_amount);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100;  
                  }else if(project_id == \"126\" && parseInt(loan_amount) <= 50000){
                    var years=(totalmonths/12);
                    var total_amount=parseInt(loan_amount);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100;
                  }else if(project_id == \"35\" && parseInt(loan_amount) <= 50000){
                    var years=(totalmonths/12);
                    var total_amount=parseInt(loan_amount);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100;
                  } else if(project_id == \"35\" && parseInt(loan_amount) > 50000){
                    var charges=Math.round(loan_amount*charges_percentage)/100;
                    var total_amount=parseInt(loan_amount)+parseInt(charges);
                    var inst_amnt = Math.ceil(total_amount / totalmonths);
                    var rounded_inst_amnt = Math.ceil(inst_amnt / 100) * 100; 
                  }else if(project_id == \"24\" && parseInt(loan_amount) <= 40000){
                    var years=(totalmonths/12);
                    var total_amount=parseInt(loan_amount);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100; 
                  }else if(project_id == \"131\" && parseInt(loan_amount) <= 30000){
                    var years=(totalmonths/12);
                    var total_amount=parseInt(loan_amount);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100;
                  }
                  else{
                    var years=(totalmonths/12);
                    var charges=Math.round(loan_amount*charges_percentage*years)/100;
                    var total_amount=parseInt(loan_amount)+parseInt(charges);
                    var inst_amnt = Math.ceil(total_amount / totalmonths);
                    var rounded_inst_amnt = Math.ceil(inst_amnt / 100) * 100;      
                  }            
             }
             $(\"#loans-inst_amnt\").val(inst_amnt);
             $(\"#loans-service_charges\").val(charges);
    }else if(product_id==15){
        if(project_id == \"131\" && product_id==15){
                if(parseInt(loan_amount) <= 30000){
                        var totalmonths = 1;
                        var inst_amnt = loan_amount;
                        var LoanInstType = \"Semi-Annually\";
                        $(\"#loans-inst_months\").val(totalmonths);
                        $(\"#loans-inst_amnt\").val(inst_amnt);
                        $(\"#loans-inst_type\").val(LoanInstType); 
                }else{
                    
                        var charges=Math.round(loan_amount*charges_percentage*0.5)/100;
                        var total_amount=parseInt(loan_amount)+parseInt(charges);
                        var rounded_inst_amnt = Math.ceil(total_amount / 100) * 100;
                        
                    var LoanInstType = \"Semi-Annually\";
                    $(\"#loans-inst_months\").val(1);
                    $(\"#loans-inst_amnt\").val(rounded_inst_amnt);
                    $(\"#loans-inst_type\").val(LoanInstType); 
                }
        }else{
            var totalmonths = 1;
                    var inst_amnt = loan_amount;
                    var LoanInstType = \"Semi-Annually\";
                    $(\"#loans-inst_months\").val(totalmonths);
                    $(\"#loans-inst_amnt\").val(inst_amnt);
                    $(\"#loans-inst_type\").val(LoanInstType);
        }
    
    }else if(project_code == \"pmifl\" || project_code == \"IFL-Ehsaas\"){
    
//        if(loan_amount > 0 && loan_amount <= 30000){
//                 var totalmonths = 12;
//                 var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
//                 var LoanInstType = \"Monthly\";
//                 $(\"#loans-inst_months\").val(totalmonths);
//                 $(\"#loans-inst_amnt\").val(inst_amnt);
//                 $(\"#loans-inst_type\").val(LoanInstType);
//        }else{
//                 var totalmonths = 15;
//                 var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
//                 var LoanInstType = \"Monthly\";
//                 $(\"#loans-inst_months\").val(totalmonths);
//                 $(\"#loans-inst_amnt\").val(inst_amnt);
//                 $(\"#loans-inst_type\").val(LoanInstType);
//                 }
                 var totalmonths = 12;
                 var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                 var LoanInstType = \"Monthly\";
                 $(\"#loans-inst_months\").val(totalmonths);
                 $(\"#loans-inst_amnt\").val(inst_amnt);
                 $(\"#loans-inst_type\").val(LoanInstType);
                 
    }/*else if(project_code == \"IICO\"){
        var totalmonths = 1;
                 var inst_amnt = loan_amount;
                 var LoanInstType = \"Nine-Monthly\";
                 $(\"#loans-inst_months\").val(totalmonths);
                 $(\"#loans-inst_amnt\").val(inst_amnt);
                 $(\"#loans-inst_type\").val(LoanInstType);
    }*/
    else if(project_code == \"lwc\" && activity_name == \"Solar TubeWell\"){
             var totalmonths = 3;
             var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
             var LoanInstType = \"Semi-Annually\";
             $(\"#loans-inst_months\").val(totalmonths);
             $(\"#loans-inst_amnt\").val(inst_amnt);
             $(\"#loans-inst_type\").val(LoanInstType);
    }
     else if(project_id == \"131\" || project_id == \"98\" || project_id == \"109\" || project_id == \"114\" || project_id == \"110\"|| project_id == \"52\" || project_id == \"103\" || project_id==61 || project_id==62 || project_id==64 || project_id==97 || project_id==113 || project_id==67 || project_id==76 || project_id==90 || project_id==87 || project_id==74 || project_id==78  || project_id==128 || project_id==113 || project_id==94 || project_id==112 || project_id==83 || project_id==96 || project_id==100 || project_id==101 || project_id==118 || project_id==119 || project_id==120 || project_id==121 || project_id==123 || project_id==125 || project_id==126 || project_id==111 || project_id==127 || project_id==24 || project_id==130 || project_id==132 || project_id==136 || project_id==139 || project_id==140 || project_id==141 || project_id==142 || project_id==35 || project_id==143 || project_id==145 || project_id==146 ){
             if(project_id == \"131\"){
                if(product_id !=15){
                    if(loan_amount <= 30000){
                        var LoanInstType = 'Monthly';
                        $('#loans-inst_type').val(LoanInstType);
                        var totalmonths = $(\"#loans-inst_months\").val();
                        if(totalmonths){
                            var years=Math.round(totalmonths/12);
                            var total_amount=parseInt(loan_amount);
                            var inst_amnt = Math.ceil((total_amount/totalmonths) / 100) *100;              
                        }
                        $(\"#loans-inst_amnt\").val(inst_amnt);
                        $(\"#loans-service_charges\").val(charges);
                    }else{
                        var LoanInstType = 'Monthly';
                        $('#loans-inst_type').val(LoanInstType);
                        var totalmonths = $(\"#loans-inst_months\").val();
                        if(totalmonths){
                            var years=Math.round(totalmonths/12);
                            var charges=(loan_amount*charges_percentage*years)/100;
                            var total_amount=parseInt(loan_amount)+parseInt(charges);
                            var inst_amnt = Math.ceil((total_amount/totalmonths) / 100) *100;              
                        }
                        $(\"#loans-inst_amnt\").val(inst_amnt);
                        $(\"#loans-service_charges\").val(charges);
                    }
                    
                }
             }else{
                var LoanInstType = 'Monthly';
                $('#loans-inst_type').val(LoanInstType);
                var totalmonths = $(\"#loans-inst_months\").val();
                if(totalmonths){
                    var years=Math.round(totalmonths/12);
                    var charges=(loan_amount*charges_percentage*years)/100;
                    var total_amount=parseInt(loan_amount)+parseInt(charges);
                    var inst_amnt = Math.round((total_amount/totalmonths) / 100) *100;              
                }
                $(\"#loans-inst_amnt\").val(inst_amnt);
                $(\"#loans-service_charges\").val(charges);
             }
            
    }else if(project_id == \"59\"){
               var LoanInstType = 'Monthly';
               $(\"#loans-inst_type\").val(LoanInstType);
               var totalmonths = $(\"#loans-inst_months\").val();
               var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
               $(\"#loans-inst_amnt\").val(inst_amnt); 
     }else if(project_id == \"122\"){
               var LoanInstType = \"Semi-Annually\";
               $(\"#loans-inst_type\").val(LoanInstType);
               var totalmonths = $(\"#loans-inst_months\").val();
               var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
               $(\"#loans-inst_amnt\").val(inst_amnt); 
    }else if(project_id == \"26\"){
                 var totalmonths = 18;
                 var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                 var LoanInstType = \"Monthly\";
                 $(\"#loans-inst_months\").val(totalmonths);
                 $(\"#loans-inst_amnt\").val(inst_amnt);
                 $(\"#loans-inst_type\").val(LoanInstType);
    }
                 
    // else if(project_id == \"60\"){
    //              var totalmonths = 20;
    //              var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
    //              var LoanInstType = \"Monthly\";
    //              $(\"#loans-inst_months\").val(totalmonths);
    //              $(\"#loans-inst_amnt\").val(inst_amnt);
    //              $(\"#loans-inst_type\").val(LoanInstType);
    // } 

    else if(project_id == \"71\"){
            var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 22000){
                 //alert(\"here\");
                     var totalmonths = 12;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 22000 && loan_amount <= 30000){
                     var inst_amnt = 2000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 30000 && loan_amount <= 50000){
                     var totalmonths = 15;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 50000){
                     var totalmonths = 20;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
        } 

        else if(project_id == \"1\"){
            var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 35000){
                 //alert(\"here\");
                     var totalmonths = 20;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 35000 && loan_amount <= 50000){
                     var totalmonths = 24;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
    }
    else{
        var LoanInstType = \"Monthly\";
                 if(loan_amount > 0 && loan_amount <= 20000){
                 //alert(\"here\");
                     var totalmonths = 12;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 20000 && loan_amount <= 30000){
                     var inst_amnt = 2000;
                     var totalmonths = Math.ceil(loan_amount/inst_amnt);
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 30000 && loan_amount <= 50000){
                     var totalmonths = 15;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }else if(loan_amount > 50000){
                     var totalmonths = 20;
                     var inst_amnt = Math.ceil((loan_amount/totalmonths) / 100) *100;
                     $(\"#loans-inst_months\").val(totalmonths);
                     $(\"#loans-inst_amnt\").val(inst_amnt);
                 }
                 $(\"#loans-inst_type\").val(LoanInstType);
    }
   
}
$(document).ready(function(){
             
                 $(\"#loans-inst_months,#loans-loan_amount,#loans-inst_amnt\").focus(calc_inst);
                 $(\"#loans-loan_amount\").change(calc_inst);
                 $(\"#loans-inst_amnt\").blur(calc_inst);
                 $(\"#loans-inst_months\").change(calc_inst);
                 //$(\"#loans-inst_amnt\").change(calc_inst);
             
 
    $(\"#loans-loan_amount\").change(function() {
              project_id=\"" . ($application->project_id) . "\";
              var loan_amount = $(\"#loans-loan_amount\").val();
              if(project_id == \"98\" || project_id == \"109\" || project_id == \"114\" || project_id == \"110\" || project_id==\"52\" || project_id==\"103\" || project_id==61 || project_id==62 || project_id==64 || project_id==97 || project_id==113 || project_id==67 || project_id==76 || project_id==90 || project_id==87 || project_id==74 || project_id==78 || project_id==128|| project_id==94 || project_id==112 || project_id==83 || project_id==100 || project_id==101  || project_id==118 || project_id==119 || project_id==126 || project_id==127 || project_id==24 || project_id==129 || project_id==130 || project_id==132 || project_id==135 || project_id==136 || project_id==137 || project_id==138 || project_id==139 || project_id==140 || project_id==141 || project_id==142 || project_id==35 || project_id==143 || project_id==145 || project_id==146){
                $(\"#loans-loan_amount\").blur(calc_summary);
                $(\"#loans-loan_amount\").change(calc_summary);
                $(\"#loans-inst_months\").change(calc_summary);
              }
              $.ajax({
               type: \"POST\",
               url: '/loans/get-tranches-detail?project_id='+project_id+'&&loan_amount='+loan_amount,
                success: function(data){
                 event.preventDefault();
                var myNode = document.getElementById(\"tranches-detail\");
                while (myNode.firstChild) {
                    myNode.removeChild(myNode.firstChild);
                }
                 var obj = $.parseJSON(data);
                 var br = document.createElement(\"hr\");
                  document.getElementById(\"tranches-detail\").appendChild(br);  

                 var col_lg_3 = document.createElement(\"div\");
                 col_lg_3.classList.add('row');
                 //col_lg_3.setAttribute(\"style\",'font-color:white');
                    var t_heading = document.createElement('h2');
                    t_heading.textContent = 'Tranches Detail';
                    document.getElementById(\"tranches-detail\").appendChild(t_heading);  

                 $.each(obj, function (key, value) {
                 
                     var form_group = document.createElement(\"div\");
                     form_group.classList.add('col-sm-'+value.div_width);
                     
                     var t = document.createElement('h3');
                     var tranch_amnt=formatNumber(value.tranch_amount);
                    t.textContent = 'Tranch Amount: '+tranch_amnt;
                    //t.setAttribute('id', 'sampleId2');
                    t.setAttribute(\"style\",'color:white;align:center;margin-top:2%;margin-left:20%');

                    //var t = document.createTextNode('Tranch Amount: '+value.tranch_amount);
                     //t.setAttribute(\"style\",'font-color:white');

                     form_group.appendChild(t);
                     form_group.setAttribute(\"style\",'background-color:green;margin-left:10px;');
                     //form_group.setAttribute(\"style\",'margin-left:1px;');
                     col_lg_3.appendChild(form_group);
                     
                
                 });
                 document.getElementById(\"tranches-detail\").appendChild(col_lg_3);  
                 
              }
            }); 
    });
 
    /*var form = document.getElementById(\"lac-form\");
    alert('a');
    document.getElementById(\"loan-save\").addEventListener(\"click\", function (event) {
        var loanamount=$(\"#loans-loan_amount\").val();
            var appid = \"" . ($application->id) . "\";
            $.ajax({
               type: \"POST\",
               url: '/loans/validate-loan-amount?id='+appid+'&&amount='+loanamount,
                success: function(data){
                                           event.preventDefault();

                var obj = $.parseJSON(data);
                st=obj.data;
              
              }
            });
          
    });*/
         
  
        
   
    
});
";
$js .= "
$('#lac-form').on('beforeSubmit', function (e) {
    /*if (!confirm(\"Are you sure to create laon?\")) {
        //return false;
    }*/
    $('#loan-save').attr('disabled','disabled');
    return true;
});
";
$this->registerJs($js);


?>
<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <div id="charges-details" class="alert alert-success alert-dismissable" style="display:none">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <!--<h4><i class="icon fa fa-check exist"></i></h4>-->
            <h1 align="center">Charges Details</h1>
            <br>
            <p align="center"><strong>Total Amount: </strong><b id="total_amount"></b></p>
            <p align="center"><strong>Annual Rate: </strong><b id="total_percentage"></b></p>
            <p align="center"><strong>Charges Per Month: </strong><b id="total_charges"></b></p>
            <p align="center"><strong>Installment Amount: </strong><b id="inst_amount"></b></p>
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>

<div class="loans-form">
    <?php $form = ActiveForm::begin(['id' => 'lac-form']); ?>
    <div class="row">
        <?= $form->field($model, 'application_id')->hiddenInput(['value' => $application->id])->label(false); ?>
        <?= $form->field($model, 'project_id')->hiddenInput(['value' => $application->project_id])->label(false); ?>
        <?= $form->field($model, 'project_table')->hiddenInput(['value' => $application->project_table])->label(false); ?>
        <?= $form->field($model, 'activity_id')->hiddenInput(['value' => $application->activity_id])->label(false); ?>
        <?= $form->field($model, 'product_id')->hiddenInput(['value' => $application->product_id])->label(false); ?>
        <?= $form->field($model, 'group_id')->hiddenInput(['value' => $application->group_id])->label(false); ?>
        <?= $form->field($model, 'region_id')->hiddenInput(['value' => $application->region_id])->label(false); ?>
        <?= $form->field($model, 'area_id')->hiddenInput(['value' => $application->area_id])->label(false); ?>
        <?= $form->field($model, 'branch_id')->hiddenInput(['value' => $application->branch_id])->label(false); ?>
        <?= $form->field($model, 'team_id')->hiddenInput(['value' => $application->team_id])->label(false); ?>
        <?= $form->field($model, 'field_id')->hiddenInput(['value' => $application->field_id])->label(false); ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'loan_amount')->textInput(['maxlength' => true, 'type' => 'number', 'placeholder' => 'Loan Amount', 'class' => 'form-control form-control-sm'])->label('Loan/Financing Amount'); ?>
        </div>
        <?php if (in_array($application->project_id, \common\components\Helpers\StructureHelper::installmentProjectsList())) { ?>
            <?php if (($application->project_id == 83) || ($application->project_id == 67) || ($application->project_id == 90)) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodPsa(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if ($application->project_id == 77) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodkamyab(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if ($application->project_id == 127) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodusaLchs(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if ($application->project_id == 132) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getApnichatapnaghar(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if ($application->project_id == 105) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodPmy(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if ($application->project_id == 97) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodLchs(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if (in_array($application->project_id, [136])) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getEhssasNujawan(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else if (in_array($application->project_id, [109])) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodAkm(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriod(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } ?>
        <?php } else if (in_array($application->project_id, [130, 131,137,138])) { ?>
            <div class="col-sm-3">
                <?php if ($application->project_id == 131 && $application->product_id == 15) { ?>
                    <?= $form->field($model, 'inst_months')->textInput(['value' => 1])->label('Installment Months'); ?>
                    <?php } else { ?>
                        <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getAkhuwatEbm(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>    
                    <?php } ?>
                </div>
        <?php } else if (in_array($application->project_id, [76])) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodAkm(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if (in_array($application->project_id, [87, 78, 120, 126, 111, 128,135,74,142,35,146])) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodkpkarobar(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if (in_array($application->project_id, [118, 123, 125])) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodScooty(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if ($application->project_id == 119) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodpmiflFB(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if (in_array($application->project_id, [24, 141])) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodppaf(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if ($application->project_id == 113) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodAlflah(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if ($application->project_id == 94 || $application->project_id == 139 || $application->project_id == 140) { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodPq(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } else if (in_array($application->project_id, [89, 92, 96, 100, 101, 129, 143,145])) { ?>
            <div class="col-sm-3">
                <?php if ($application->project_id == 96 || $application->project_id == 100 || $application->project_id == 101 || $application->project_id == 129 || $application->project_id == 143) { ?>
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodMusharqa(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                <?php } else { ?>
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodRama(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                <?php } ?>
            </div>
        <?php } else if ($application->project_id == 121) { ?>
            <?php if (in_array($application->product_id, [1, 13, 14, 16])) { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->dropDownList(\common\components\Helpers\LoanHelper::getLoanPeriodMusharqa(), ['prompt' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } else { ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'inst_months')->textInput(['readonly' => 'readonly', 'maxlength' => true, 'type' => 'number', 'placeholder' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'inst_months')->textInput(['readonly' => 'readonly', 'maxlength' => true, 'type' => 'number', 'placeholder' => 'Installment Months', 'class' => 'form-control form-control-sm'])->label('Installment Months'); ?>
            </div>
        <?php } ?>
        <div class="col-sm-3" style="display: none">
            <?= $form->field($model, 'service_charges')->textInput(['readonly' => 'readonly', 'maxlength' => true, 'type' => 'number', 'placeholder' => 'Service Charges', 'class' => 'form-control form-control-sm'])->label('Fixed Rent'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'inst_amnt')->textInput(['readonly' => 'readonly', 'maxlength' => true, 'type' => 'number', 'placeholder' => 'Installment Amount', 'class' => 'form-control form-control-sm'])->label('Installment Amount'); ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'inst_type')->textInput(['readonly' => 'readonly', 'class' => 'form-control form-control-sm'])/*->dropDownList( \common\components\Helpers\ListHelper::getLists('installments_types'), ['prompt' => 'Installment Type', 'class' => 'form-control form-control-sm'])*/
            ->label('Installment Type') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, "date_approved")->widget(\yii\jui\DatePicker::className(), [
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Date Approved']
            ])->label('Date Approved'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'remarks')->textarea(['rows' => 1]) ?>
        </div>
    </div>
    <div id="tranches-detail">
    </div>
    <br>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'loan-save']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>

