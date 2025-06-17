<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Paperless Admin Portal',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
   /* $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
    ];*/
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];


    } else {
        $menuItems[] = [
            'label' => 'Credit',
            'items'=>[
                /*['label' =>'Members','url'=>'/members/index'],*/
                ['label' =>'Members Search','url'=>'/members/index-search'],
                /*['label' =>'Applications','url'=>'/applications/index'],*/
                ['label' =>'Applications Search','url'=>'/applications/index-search'],
                ['label' =>'Cib Search','url'=>'/cib/index-search'],
                ['label' =>'Groups','url'=>'/groups/index-search'],
                ['label' =>'Appraisals','url'=>'/appraisals/index'],
                ['label' =>'Business Appraisal','url'=>'/business-appraisal/index-search'],
                ['label' =>'Social Appraisal','url'=>'/social-appraisal/index-search'],
                ['label' =>'Agriculture Appraisal','url'=>'/appraisals-agriculture/index-search'],
                ['label' =>'Housing Appraisal','url'=>'/appraisals-housing/index-search'],

                /*['label' =>'Loans','url'=>'/loans/index'],*/
                ['label' =>'Loans Search','url'=>'/loans/index-search'],
                ['label' =>'Loan Tranches','url'=>'/loan-tranches/index-search'],
                ['label' =>'Disbursements','url'=>'/disbursement/index'],
                ['label' =>'Disbursement Details','url'=>'/disbursement-details/index'],
                ['label' =>'Schedules','url'=>'/schedules/index'],
                /*['label' =>'Recoveries','url'=>'/recoveries/index'],*/
                ['label' =>'Recoveries Search','url'=>'/recoveries/index-search'],
                ['label' =>'Donations','url'=>'/donations/index'],
                ['label' =>'Donations Search','url'=>'/donations/index-search'],
                ['label' =>'Projects','url'=>'/projects/index'],
                ['label' =>'Users','url'=>'/users/index'],
                ['label' =>'Blacklist Members','url'=>'/blacklist/index'],
                ['label' =>'AWP','url'=>'/awp/index'],
                ['label' =>'AWP Search','url'=>'/awp/index-search'],
                ['label' =>'AWP Target Vs Achievement','url'=>'/awp-target-vs-achievement/index'],
                ['label' =>'AWP Branch Sustainability','url'=>'/awp-branch-sustainability/index'],
                ['label' =>'Random Members','url'=>'/random-members/index'],
                ['label' =>'Emergency Loans','url'=>'/emergency-loans/index'],
                ['label' =>'Member Info','url'=>'/member-info/index'],
//                ['label' =>'Nadra Verisys','url'=>'/nadra-verisys/index'],
            ]
        ];
        $menuItems[] = [
            'label' => 'User Roles',
            'items'=>[
                ['label' =>'Reports Roles','url'=>'/designations/index'],
                '<li class="divider"></li>',
                ['label' =>'Mobile Roles','url'=>'/mobile-permissions/index'],
                ['label' =>'Reports Roles','url'=>'/designations/index'],
                '<li class="divider"></li>',
                ['label' =>'Web Roles','url'=>'/myrbac/index'],
                /* ['label' =>'Rules','url'=>'/rbac/rule/index'],
                 ['label' =>'Roles','url'=>'/rbac/role/index'],
                 ['label' =>'Permissions','url'=>'/rbac/permission/index'],
                 ['label' =>'Assingments','url'=>'/rbac/assignment/index'],*/
            ]
        ];
        /*$menuItems[] = [
            'label' => 'Progress Report',
            'items'=>[
                ['label' =>'Progress Reports','url'=>'/progress-reports/index'],
            ]
        ];*/
        $menuItems[] = [
            'label' => 'Reports',
            'items'=>[
                ['label' =>'Progress Reports','url'=>'/progress-reports/index'],
                ['label' =>'Progress Report Update','url'=>'/progress-report-update/index'],
                ['label' =>'User Report','url'=>'/users/user-report'],
                ['label' =>'Archive Reports','url'=>'/archive-reports/index'],
                ['label' =>'Aging Reports','url'=>'/aging-reports/index'],
                ['label' =>'Account Reports','url'=>'/account-reports/index'],
                ['label' =>'Branch wise Usage Report','url'=>'/branches/usage-report'],
                ['label' =>'Admin Controls','url'=>'/setting/index'],
            ]
        ];
        $menuItems[] = [
            'label' => 'Analytics',
            'items'=>[
                ['label' =>'User Analytics','url'=>'/analytics/index'],
                ['label' =>'API Analytics','url'=>'/analytics/analytics']
            ]
        ];
        $menuItems[] = [
            'label' => 'Structure',
            'url' => ['/branches/index'],
            'items'=>[
                ['label' =>'Credit Divisions','url'=>'/credit-divisions/index'],
                ['label' =>'Regions','url'=>'/regions/index'],
                ['label' =>'Areas','url'=>'/areas/index'],
                ['label' =>'Branches','url'=>'/branches/index'],
                ['label' =>'Teams','url'=>'/teams/index'],
                ['label' =>'Fields','url'=>'/fields/index'],


                '<li class="divider"></li>',
                //  '<li class="dropdown-header">Demo</li>',
                ['label' =>'Countries','url'=>'/countries/index'],
                ['label' =>'Provinces','url'=>'/provinces/index'],
                ['label' =>'Divisions','url'=>'/divisions/index'],
                ['label' =>'Cities','url'=>'/cities/index'],
                ['label' =>'Districts','url'=>'/districts/index'],
                /*['label' =>'Tehsils','url'=>'/tehsils/index'],*/
                '<li class="divider"></li>',
                ['label' =>'Branch Requests','url'=>'/branch-requests/index'],
                '<li class="divider"></li>',
                ['label' =>'Structure Transfer','url'=>'/structure-transfer/index'],

            ]
        ];
        $menuItems[] = [
            'label' => 'Dynamic Forms',
            'items'=>[
                ['label' =>'View Sections','url'=>'/view-sections/index'],
                ['label' =>'Lists','url'=>'/lists/index'],
                ['label' =>'Sort List','url'=>'/lists/datalists'],
            ]
        ];

        $menuItems[] = [
            'label' => 'Actions',
            'items'=>[
                ['label' =>'Application Actions','url'=>'/application-actions/index-search'],
                ['label' =>'Group Actions','url'=>'/group-actions/index-search'],
                ['label' =>'Loan Actions','url'=>'/loan-actions/index-search'],
                ['label' =>'Loan Tranches Actions','url'=>'/loan-tranches-actions/index-search'],
                ['label' =>'Import Data','url'=>'/import/index'],
            ]
        ];
        $menuItems[] = [
            'label' => 'Settings',
            'items'=>[
                /*['label' =>'App Settings','url'=>'/settings/index'],
                '<li class="divider"></li>',*/
                ['label' =>'Accounts','url'=>'/accounts/index'],
                ['label' =>'Accounts Update','url'=>'/setting/member-account-update'],
                ['label' =>'Activities','url'=>'/activities/index'],
                ['label' =>'Devices','url'=>'/devices/index'],
                /*['label' =>'Doners','url'=>'/doners/index'],*/
                ['label' =>'Operations','url'=>'/operations/index'],
                ['label' =>'Products','url'=>'/products/index'],
                '<li class="divider"></li>',
                ['label' =>'Loan Write Off','url'=>'/loan-write-off/index'],
                '<li class="divider"></li>',
                /*['label' =>'Accounts','url'=>'/bank-accounts/index'],*/
                ['label' =>'Banks','url'=>'/banks/index'],
                ['label' =>'Account Types','url'=>'/account-types/index'],
                ['label' =>'Members Accounts','url'=>'/members-account/index'],
                '<li class="divider"></li>',
                ['label' =>'Configuration Rules','url'=>'/config-rules/index'],
                '<li class="divider"></li>',
                ['label' =>'Versions','url'=>'/versions/index'],
                '<li class="divider"></li>',
                ['label' =>'App Banners','url'=>'/app-images/index'],
                '<li class="divider"></li>',
                ['label' =>'Cache Clear','url'=>'/cache/index'],



            ]
        ];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
