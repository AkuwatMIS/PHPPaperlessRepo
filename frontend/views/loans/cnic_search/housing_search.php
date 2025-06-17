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

?>

<?php

echo $this->render('_search_housing', [
    'searchModel' => $searchModel,
    'types' => $types
]);
if (!empty($applications)) {
    ?>
    <div class="container-fluid">

        <?php foreach ($applications as $application) { ?>
            <div id="demo" style="border:1px solid #d6e9c6;padding:10px">
                <h3><?= $application->application_no ?></h3>
                <section class="card mb-3">
                    <header class="card-header card-header-lg">
                        Applications Info
                    </header>
                    <div class="profile-info-item" style="margin-top: 15px;">
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <b>Project</b> : <?php echo $application->project->name; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <b>Application No</b> : <?php echo $application->application_no; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <b>Application Date</b>
                                    : <?= date('d M,Y', $application->application_date); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <b>Application Status</b>
                                    : <?= $application->status; ?>
                                </p>
                            </div>

                        </div>
                    </div>
                </section>
                <section class="card mb-3">
                    <header class="card-header card-header-lg">
                        Appraisals Info
                    </header>
                    <article class="profile-info-item">
                        <div class="box-typical-inner">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Assign to</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($application->actions as $key => $action) {
                                        if (in_array($action->action, ['social_appraisal', 'housing_appraisal', 'business_appraisal'])) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo ucfirst($action->action) ?>
                                                    <br>
                                                    <p style="font-size: 10px;color: green;">
                                                        Created
                                                        at: <?= ($action->created_at != 0) ? date('d M Y H:i', $action->created_at) : '-' ?>
                                                    </p>
                                                    <p style="font-size: 10px;color: green;">
                                                        Last Updated
                                                        at: <?= ($action->updated_at != 0) ? date('d M Y H:i', $action->updated_at) : '-' ?>
                                                    </p>
                                                </td>
                                                <td><?php echo $action->user->fullname; ?></td>
                                                <td><?php echo ($action->status == 1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </section>
                <section class="card mb-3">
                    <header class="card-header card-header-lg">
                        Group Info
                    </header>
                    <div class="profile-info-item" style="margin-top: 15px;">
                        <div class="row">
                            <?php if ($application->group_id != 0) { ?>
                                <div class="col-md-6">
                                    <p>
                                        <b>Group Type</b> : <?php echo $application->group->grp_type; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <b>Group No</b> : <?php echo $application->group->grp_no; ?>
                                    </p>
                                </div>
                            <?php } else { ?>
                                <div class="profile-info-item" style="margin-top: 15px;">
                                    <div class="row">
                                        <p>Groupe Formation is not done yet!</p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <?php if (!empty($application->loan)) { ?>
                    <?php foreach ($application->loan->tranchesSorted as $tranch) { ?>
                        <section class="card mb-3">
                            <header class="card-header card-header-lg">
                                Tranche-<?= $tranch->tranch_no ?> Info
                            </header>
                            <div class="profile-info-item" style="margin-top: 15px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>
                                            <b>Tranche Status</b> : <?php echo $tranch->status; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Step</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Ready For Fund Request</td>
                                            <td><?= ($tranch->status > 3) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        <tr>
                                            <td>Fund Request</td>
                                            <td><?= ($tranch->fund_request_id != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        <tr>
                                            <td>Cheque Printing</td>
                                            <td><?= ($tranch->cheque_no != 0 || $tranch->cheque_no != '') ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        <tr>
                                            <td>Disbursement</td>
                                            <td><?= ($tranch->status > 5) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        <tr>
                                            <td>Publish</td>
                                            <td><?= !empty($tranch->publish) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        <tr>
                                            <td>Funds Transfered</td>
                                            <td><?= ($tranch->status == 6) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    <?php } ?>

                <?php } else { ?>
                    <section class="card mb-3">
                        <header class="card-header card-header-lg">
                            Loan Info
                        </header>
                        <div class="profile-info-item" style="margin-top: 15px;">
                            <div class="row">
                                <p>loan against this application is not created</p>
                            </div>
                        </div>
                    </section>
                <?php } ?>
                </section>
                <hr>
            </div>
        <?php } ?>
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
Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end();
?>


