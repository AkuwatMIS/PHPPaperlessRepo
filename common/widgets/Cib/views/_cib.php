<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */

/*echo '<pre>';
print_r(count($model->recoveries));
die("we die here");*/
$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
$response = $model;
?>
<p>
    <section style=" float: left;">
        <div class="left" style="margin-bottom:10px;float: left;width: 120px;">
            <?php echo Html::img('@frontend/web/images/akhuwat-logo.png', ['alt' => Yii::$app->name, 'class' => 'hidden-md-down']); ?>
        </div>
        <div class="left" style="margin-top:100px;float: left;width: 430px;">
            <span style=" font-size:25px;float: left">&nbsp;&nbsp;Consumer Credit Information Report</span>
        </div>
        <div class="left" style="float: left;width: 150px;">
            <span style="font-weight:bold;text-align: center; font-size:12px;">CONFIDENTIAL</span>
            <br>
            <span style=" font-size:12px;float: right">Date Time: <?php echo $response->reportDate?></span>
            <br>
            <span style=" font-size:12px;float: right">Report Ref No: <?php echo $response->refNo?></span>
        </div>

    </section>

    <?php /*echo Html::img('@web/images/akhuwat-logo.png', ['alt' => Yii::$app->name, 'class' => 'hidden-md-down']); */?><!--
    <span style=" font-size:25px;float: left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consumer Credit Information Report</span>
    <span style=" font-size:5px;float: right">Report date</span>-->
</p>

<div style="height:1px;background-color: #2e75b6;">
    <h6 style="padding-top:-15px;padding-bottom: -15px;color: white"> &nbsp; &nbsp;PERSONAL INFORMATION</h6>
</div>
<div style="border: 1px solid black;font-size: 10px;">
    <section style=" float: left;">
        <div class="left" style="margin-left:10px;float: left;width: 200px;">
            <b>Name: </b><?= $response->personalInformation->NAME ?>
            <br>
            <b>Gender: </b><?= $response->personalInformation->GENDER ?>
            <br>
            <b>Date of Birth: </b><?= $response->personalInformation->DOB ?>
            <br>
            <b>Nationality: </b><?= $response->personalInformation->NATIONALITY ?>
        </div>
        <div class="left" style="float: left;width: 200px;">

            <b>CNIC: </b><?= $response->personalInformation->CNIC ?>
            <br>
            <!--<b>NIC: </b><?/*= $response->personalInformation->NIC */?>
            <br>
            <b>NTN: </b><?/*= $response->personalInformation->NTN */?>
            <br>-->
        </div>
        <div class="left" style="float: left;width: 200px;">
            <b>Father/Husband Name: </b><?= $response->personalInformation->FATHER_OR_HUSBAND_NAME ?>
            <br>
            <!--<b>Passport No: </b><?/*= $response->personalInformation->PASSPORT */?>
            <br>-->
            <b>Business Profession: </b><?= $response->personalInformation->BUSINESS_OR_PROFESSION ?>
            <br>
            <b>Borrower Type: </b><?= $response->personalInformation->BORROWER_TYPE ?>
        </div>

    </section>
    <br>
    <div style="margin-left: 10px">
        <p style="line-height: 2px"><b>Current Residential
                Address: </b><?= $response->personalInformation->CURRENT_RESIDENTIAL_ADDRESS ?></p>
        <p style="line-height: 2px"><b>Permanent Address: </b><?= $response->personalInformation->PERMANENT_ADDRESS ?>
        </p>
        <p style="line-height: 2px"><b>Previouse Residential
                Address: </b><?= $response->personalInformation->PREVIOUS_RESIDENTIAL_ADDRESS ?></p>
        <!--<p style="line-height: 2px"><b>Employer
                Business: </b><?/*= $response->personalInformation->EMPLOYER_OR_BUSINESS */?>
        </p>-->
    </div>
</div>
<br>
<div style="background-color: #2e75b6;">
    <h6 style="padding-top:-15px;padding-bottom: -15px;color: white"> &nbsp; &nbsp;SUMMARY OF INFORATION CONTAINED IN THIS REPORT</h6>
</div>
<section style="border:  1px solid black; float: left;font-size: 10px;">
    <div class="left" style="float: left;width: 230px; text-align: center">
        <div style="padding-top:10px;color: black; margin-right:35px; margin-left:30px;margin-top: 10px;height: 30px;background-color: #d0cece;"
             class="rectangle"><b><?= $response->noOfCreditEnquiry ?></b></div>
        <h5>No of Credit Enquires(Last 12 Months)</h5>
    </div>
    <div class="left" style="float: left;width: 230px; text-align: center">
        <div style="padding-top:10px;color: black; height: 30px;margin-right:35px;margin-top: 10px;background-color: #d0cece;"
             class="rectangle"><b><?= $response->noOfActiveAccounts ?></b></div>
        <h5>No of Active Accounts</h5>
    </div>
    <div class="left" style="float: left;width: 230px; text-align: center">
        <div style="padding-top:10px;color: black; margin-right:35px; height: 30px;margin-top: 10px;background-color: #d0cece;"
             class="rectangle"><b>PKR <?= ($response->totalOutstandingBalance!='*')?number_format($response->totalOutstandingBalance): $response->totalOutstandingBalance ?></b></div>
        <h5>Total Outstanding Balance</h5>
    </div>

</section>
<!--<br>
<div style="background-color: #2e75b6;">
    <h6 style="color: white"> &nbsp; &nbsp;OVERDUE SUMMARY OF LOANS FOR LAST 24 MONTHS</h6>
</div>
<table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;font-size: 10px;'>
    <thead>
    <tr>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px"></th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">30+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">60+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">90+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">120+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">150+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">180+</th>
        <th style="width:12%;border: 1px solid #ddd; text-align: center;font-size: 10px">MFI Default</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="width:12%;border: 1px solid #ddd;">No of Times</td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_30_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_60_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_90_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_120_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_150_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->PLUS_180_24M */?></td>
        <td style="width:12%;border: 1px solid #ddd;text-align: center;"><?/*= $response->summaryOverdue_24M->MFI_DEFAULT */?></td>
    </tr>
    </tbody>
</table>-->
<br>
<!--<div style="padding-top:-15px;padding-bottom: -15px;height:10px;background-color: #2e75b6;">
    <h6 style="color: white"> &nbsp; &nbsp;STATUS OF CREDIT APPLICATIONS FOR LAST 24 MONTHS</h6>
</div>
<table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%'>
    <thead>
    <tr>
        <th style="width:50%;border: 1px solid #ddd; text-align: center;font-size: 10px">Product</th>
        <th style="width:50%;border: 1px solid #ddd; text-align: center;font-size: 10px">Financial Institution</th>
        <th style="width:50%;border: 1px solid #ddd; text-align: center;font-size: 10px">Date of Application</th>
        <th style="width:50%;border: 1px solid #ddd; text-align: center;font-size: 10px">Amount of Fecility</th>
        <th style="width:50%;border: 1px solid #ddd; text-align: center;font-size: 10px">Status</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="width:50%;border: 1px solid #ddd;text-align: center;"><?/*= $response->detailsOfStatusCreditApplication->PRODUCT */?></td>
        <td style="width:50%;border: 1px solid #ddd;text-align: center;"><?/*= $response->detailsOfStatusCreditApplication->FINANCIAL_INSTITUTION */?></td>
        <td style="width:50%;border: 1px solid #ddd;text-align: center;"><?/*= $response->detailsOfStatusCreditApplication->DATE_OF_APPLICATION */?></td>
        <td style="width:50%;border: 1px solid #ddd;text-align: center;"><?/*= $response->detailsOfStatusCreditApplication->AMOUNT_OF_FACILITY */?></td>
        <td style="width:50%;border: 1px solid #ddd;text-align: center;"><?/*= $response->detailsOfStatusCreditApplication->STATUS */?></td>
    </tr>
    </tbody>
</table>
<br>-->
<div style="padding-top:-15px;padding-bottom: -15px;background-color: #2e75b6;">
    <h6 style="color: white"> &nbsp; DETAIL <!--OF SETTLEMENT-->OF LOANS FOR LAST FIVE YEARS</h6>
</div>
<table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;font-size: 10px;'>
    <thead>
    <tr>
        <th colspan="4" style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 15px"></th>
        <th colspan="5" style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 15px">Overdue Details</th>

    </tr>
    <tr>
        <!--<th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px ">Product</th>-->
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Loan Amount</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">OLP</th>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Approval Date</th>
        <!--<th style="width:20%;border: 1px solid #ddd; text-align: center;">Relationship Date</th>-->
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">Maturity Date</th>
        <!--<th style="width:30%;border: 1px solid #ddd; text-align: center;">Date of Settlement</th>-->

        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">30+</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">60+</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">90+</th>
        <!--<th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">120+</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">150+</th>-->
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">180+</th>
        <!--<th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">MFI Default</th>-->
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">Status</th>

    </tr>
    </thead>
    <tbody>
    <?php
    $overdue_count = 0;
    foreach ($response->detailsOfLoansSettlement as $setlement) {
        ?>
        <tr>
            <!--<td style="width:20%;border: 1px solid #ddd;"><?/*= $setlement->PRODUCT */?></td>-->
            <!--<td style="width:20%;border: 1px solid #ddd;text-align: center;"><?/*= number_format($setlement->TOTAL_LIMIT) */?></td>-->
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= (($response->loanDetails[$overdue_count]->LOAN_LIMIT!='*') && isset($response->loanDetails[$overdue_count]->LOAN_LIMIT)) ? number_format($response->loanDetails[$overdue_count]->LOAN_LIMIT) : '' ?></td>
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= (($response->loanDetails[$overdue_count]->OUTSTANDING_BALANCE!='*') && isset($response->loanDetails[$overdue_count]->OUTSTANDING_BALANCE)) ? number_format($response->loanDetails[$overdue_count]->OUTSTANDING_BALANCE) : '*' ?></td>
            <!--<td style="width:20%;border: 1px solid #ddd;text-align: center;"><?/*= $setlement->APPROVAL_DATE */?></td>-->
            <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= isset($response->loanDetails[$overdue_count]->FACILITY_DATE) ? $response->loanDetails[$overdue_count]->FACILITY_DATE:''?></td>
            <!--<td style="width:20%;border: 1px solid #ddd;text-align: center;"><?/*= $setlement->RELATIONSHIP_DATE */?></td>-->
            <!--<td style="width:30%;border: 1px solid #ddd;text-align: center;"><?/*= $setlement->MATURITY_DATE */?></td>-->
            <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= isset($response->loanDetails[$overdue_count]->MATURITY_DATE) ? $response->loanDetails[$overdue_count]->MATURITY_DATE:''?></td>
            <!--<td style="width:30%;border: 1px solid #ddd;text-align: center;"><?/*= $setlement->DATE_OF_SETTLEMENT */?></td>-->

            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->loanDetails[$overdue_count]->PLUS_30 ?></td>
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->loanDetails[$overdue_count]->PLUS_60 ?></td>
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->loanDetails[$overdue_count]->PLUS_90 ?></td>
            <!--<td style="width:30%;border: 1px solid #ddd;text-align: center;"><?/*= $response->loanDetails[$overdue_count]->PLUS_120 */?></td>
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?/*= $response->loanDetails[$overdue_count]->PLUS_150 */?></td>-->
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->loanDetails[$overdue_count]->PLUS_180 ?></td>
            <!--<td style="width:30%;border: 1px solid #ddd;text-align: center;"><?/*= $response->loanDetails[$overdue_count]->MFI_DEFAULT */?></td>-->
            <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= ($response->loanDetails[$overdue_count]->OUTSTANDING_BALANCE==0)? 'Closed':'Open' ?></td>

        </tr>
        <?php $overdue_count++;
    } ?>
    </tbody>
</table>
<br>
<div style="padding-top:-15px;padding-bottom: -15px;background-color: #2e75b6;">
    <h6 style="color: white"> &nbsp; &nbsp;DETAIL OF PERSONAL GUARANTEES GIVEN BY APPLICANT</h6>
</div>
<table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;font-size: 10px;'>
    <thead>
    <tr>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px"></th>
        <th colspan="2" style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Principle Borrower</th>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px"></th>
        <th colspan="2" style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px ">Guarantee</th>

    </tr>
    <tr>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Product</th>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">NAME</th>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">CNIC</th>
        <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Date of Invocation</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">Date</th>
        <th style="width:30%;border: 1px solid #ddd; text-align: center;font-size: 10px">Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->PRODUCT ?></td>
        <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->PRINCIPAL_BORROWER_NAME ?></td>
        <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->PRINCIPAL_BORROWER_CNIC ?></td>
        <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->DATE_OF_INVOCATION ?></td>
        <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->GUARANTEE_DATE ?></td>
        <td style="width:30%;border: 1px solid #ddd;text-align: center;"><?= $response->personalGuarantees->GUARANTEE_AMOUNT ?></td>
    </tr>
    </tbody>
</table>
<br>
<section style=" float: left;">
    <div class="left" style="margin-left:0px;float: left;width: 300px;">
        <div style="background-color: #2e75b6">
            <h6 style="padding-top:-15px;padding-bottom: -15px;color: white"> &nbsp; &nbsp;DETAIL OF CO-BORROWER OF APPLICANT</h6>
        </div>
        <table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;'>
            <thead>
            <tr>
                <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name
                </th>
                <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CNIC&nbsp;&nbsp;&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:20%;border: 1px solid #ddd;text-align: center;font-size: 10px"><?= $response->coborrowerDetail->NAME ?></td>
                <td style="width:20%;border: 1px solid #ddd;text-align: center;font-size: 10px"><?= $response->coborrowerDetail->CNIC ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="left" style="float: right;margin-left: 50px;width: 300px;">
        <div style="padding-top:-15px;padding-bottom: -15px;background-color: #2e75b6">
            <h6 style="color: white"> &nbsp; &nbsp;DETAIL OF BANKRUPTCY CASES</h6>
        </div>
        <table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;'>
            <thead>
            <tr>
                <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Court Name</th>
                <th style="width:20%;border: 1px solid #ddd; text-align: center;font-size: 10px">Declaration Date</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->detailsOfBankruptcyCases->COURT_NAME ?></td>
                <td style="width:20%;border: 1px solid #ddd;text-align: center;"><?= $response->detailsOfBankruptcyCases->DECLARATION_DATE ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
<div style="padding-top:-15px;padding-bottom: -15px;background-color: #2e75b6">
    <h6 style="color: white"> &nbsp; &nbsp;DETAIL OF ENQUIRIES FOR LAST 24 MONTHS</h6>
</div>
<table style='font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;'>
    <thead>
    <tr>
        <th style="width:80%;border: 1px solid #ddd; text-align: center; ">Sr.</th>
        <th style="width:80%;border: 1px solid #ddd; text-align: center;">FI Type</th>
        <th style="width:80%;border: 1px solid #ddd; text-align: center;">Date of Enquiry</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($response->creditEnquiry as $enquiry) { ?>
    <tr>
        <td style="width:80%;border: 1px solid #ddd;text-align: center;"><?= $enquiry->SR_NO  ?></td>
        <td style="width:80%;border: 1px solid #ddd;text-align: center;"><?= $enquiry->FI_TYPE  ?></td>
        <td style="width:80%;border: 1px solid #ddd;text-align: center;"><?= $enquiry->DATE_OF_ENQUIRY  ?></td>
    </tr>
    <?php }  ?>
    </tbody>
</table>