<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use common\themes\startui\assets\AssetApp;

\common\themes\startui\assets\AssetApp::register($this);
\frontend\assets\AppAsset::register($this);
if (empty(Yii::$app->session->get('permissions')))
{
    $auth = Yii::$app->authManager;
    $permissionslist = ($auth->getPermissionsByUser(Yii::$app->user->getId()));
    $permissions = [];
    foreach ($permissionslist as $key => $value) {
        $permissions[] = $key;
    }
    Yii::$app->session->set('permissions',$permissions);
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head lang="en">

    <?= Html::csrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>

<body class="with-side-menu control-panel control-panel-compact">
<?php $this->beginBody() ?>

<?= $this->render('_header'); ?>

<div class="mobile-menu-left-overlay"></div>

<?= $this->render('_sidebar'); ?>

<div class="page-content">

    <?=
    \yii\widgets\Breadcrumbs::widget([
        'homeLink' => [
            'label' => Yii::t('yii', 'Home'),
            'url' => Yii::$app->homeUrl,
        ],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ])
    ?>

    <?= $content; ?>
</div><!--.page-content-->

<!--<? /*= $this->render('_control_panel'); */ ?>-->
<?php

?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-99828028-3"></script>
<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-99828028-3');
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
