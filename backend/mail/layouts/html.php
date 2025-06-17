<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

        <style>
            @font-face {
                font-family: 'Avenir';
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/Avenir-Heavy.eot');
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/Avenir-Heavy.eot?#iefix') format('embedded-opentype'), url('fonts/Avenir-Heavy.woff2') format('woff2'), url('fonts/Avenir-Heavy.woff') format('woff'), url('fonts/Avenir-Heavy.ttf') format('truetype'), url('fonts/Avenir-Heavy.svg#Avenir-Heavy') format('svg');
                font-weight: 900;
                font-style: normal;
            }

            @font-face {
                font-family: 'Avenir';
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/AvenirLT-Black.eot');
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/AvenirLT-Black.eot?#iefix') format('embedded-opentype'), url('fonts/AvenirLT-Black.woff2') format('woff2'), url('fonts/AvenirLT-Black.woff') format('woff'), url('fonts/AvenirLT-Black.ttf') format('truetype'), url('fonts/AvenirLT-Black.svg#AvenirLT-Black') format('svg');
                font-weight: 700;
                font-style: normal;
            }

            @font-face {
                font-family: 'Avenir';
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/AvenirLT-Roman.eot');
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/AvenirLT-Roman.eot?#iefix') format('embedded-opentype'), url('fonts/AvenirLT-Roman.woff2') format('woff2'), url('fonts/AvenirLT-Roman.woff') format('woff'), url('fonts/AvenirLT-Roman.ttf') format('truetype'), url('fonts/AvenirLT-Roman.svg#AvenirLT-Roman') format('svg');
                font-weight: normal;
                font-style: normal;
            }

            @font-face {
                font-family: 'Avenir';
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/Avenir-Medium.eot');
                src: url('<?php echo Url::to('@web', true) ?>/email/fonts/Avenir-Medium.eot?#iefix') format('embedded-opentype'), url('fonts/Avenir-Medium.woff2') format('woff2'), url('fonts/Avenir-Medium.woff') format('woff'), url('fonts/Avenir-Medium.ttf') format('truetype'), url('fonts/Avenir-Medium.svg#Avenir-Medium') format('svg');
                font-weight: 500;
                font-style: normal;
            }
            h2 {
                font-family: 'Avenir';
                font-weight: 500;
                color: #3f454c;
                font-size: 22px;
                margin: 50px 0 25px;
            }
            h3 {
                font-family: 'Avenir';
                font-weight: 900;
                color: #3f454c;
                font-size: 16px;
                margin: 0px;
            }
            p {
                font-family: 'Avenir';
                font-size: 16px;
                font-weight: normal;
                line-height: 24px;
                margin: 0 0 20px;
                color: #3f454c;
            }
            a {
                color: #83be5f;
                text-decoration: none;
            }
            .btn {
                background: #83be5f;
                border-radius: 5px;
                color: #fff;
                text-decoration: none;
                line-height: 50px;
                display: inline-block;
                padding: 0 30px;
                font-family: 'Avenir';
                font-weight: 900;
                font-size: 18px;
                margin: 10px 0 25px
            }
            .footer a {
                color: #3d84d9;
                font-family: 'Avenir';
                font-size: 13px;
                padding: 0 5px
            }
            .msg {
                color: #83be5f;
                font-family: 'Avenir';
                font-weight: 900;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <div style="border:1px solid #f8f9f9; width:90%; margin:auto; border-radius:5px; position:relative;">
            <?php $this->beginBody() ?>
            <?= $content ?>
            <?php $this->endBody() ?>
            <table style="background:#f7f8f8; margin:50px auto 0; position:absolute; left:0; bottom:0px;" width="100%" border="0" cellspacing="0" cellpadding="20">
                <tr>
                    <td class="footer" style="text-align:center"><a href="#">Help</a>|<a href="#">Terms and Privacy</a></td>
                </tr>
            </table>
        </div>
    </body>
</html>
<?php $this->endPage() ?>
