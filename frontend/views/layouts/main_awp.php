<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use common\themes\startui\assets\AssetSimple;

AssetSimple::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head lang="en">

    <style>

        .awp-overdue .grid-view table thead  {display: none;}

        .awp-overdue-index.expend-cell-tb .grid-view table tbody tr td:first-child,
        .awp-overdue .grid-view table tbody tr td:first-child {width: 20%;}

        .awp-overdue .grid-view table tbody tr td:first-child {font-size: 0px !important;}

        .awp-overdue-index.expend-cell-tb .grid-view table tbody tr td,
        .awp-overdue .grid-view table tbody tr td {
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
            font-size: 20px;
            width:11.4285%;
        }

        .awp-overdue-index.expend-cell-tb .grid-view table tbody  tr[id^="expand-row-column-detail"]>td { padding:0 }

        .awp-overdue-index.expend-cell-tb .grid-view table tbody tr[id^="expand-row-column-detail"] {border-left: 0px;}

        .awp-overdue .grid-view table.table-hover tbody tr {background: rgba(0,0,0,.20); transition: all ease-in-out 0.3s;}

        .awp-overdue .grid-view table.table-hover tbody tr:hover {
            background: #f1f1f1;
        }



    </style>



</head>

<body>
<?php $this->beginBody() ?>


<?= $content; ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-99828028-3"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-99828028-3');
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
