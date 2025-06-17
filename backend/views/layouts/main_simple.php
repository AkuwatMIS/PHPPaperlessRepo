<?php

/**
 * @var string $content
 * @var \yii\web\View $this
 */

use yii\helpers\Html;

//$bundle = yiister\gentelella\assets\Asset::register($this);

?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>

    </head>

    <?php $this->beginBody(); ?>
             <?= $content ?>
    <?php $this->endBody(); ?>
   <!-- </body>-->
    </html>
<?php $this->endPage(); ?>