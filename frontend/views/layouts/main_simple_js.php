<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use common\themes\startui\assets\AssetSimple;

\common\themes\startui\assets\AssetApp::register($this);
\frontend\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head lang="en">

    <?= Html::csrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= Html::encode($this->title) ?></title>

    <link href="img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
    <link href="img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
    <link href="img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
    <link href="img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">
    <link href="img/favicon.png" rel="icon" type="image/png">
    <link href="img/favicon.ico" rel="shortcut icon">

    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>


<?= $content; ?>



<script>
    $(function () {
        $('.page-center').matchHeight({
            target: $('html')
        });

        $(window).resize(function () {
            setTimeout(function () {
                $('.page-center').matchHeight({remove: true});
                $('.page-center').matchHeight({
                    target: $('html')
                });
            }, 100);
        });
    });
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
