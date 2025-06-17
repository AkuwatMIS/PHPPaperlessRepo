<?php
/**
 * Created by PhpStorm.
 * User: DELl
 * Date: 2/4/2017
 * Time: 2:58 PM
 */
use common\components\ConversionCalculationHelper;
use yii\helpers\Url;

?>

<table width="475" border="0" cellspacing="0" cellpadding="0" style="margin:60px auto;">

    <tr>
        <td style="text-align:center;">
            <img src="<?php echo Url::to('@web', true); ?>/email/images/email-logo.png" style="width: 120px; height: 128px;" alt="">
        </td>
    </tr>

    <tr>
        <td><h2><?= $user->first_name ?>, Chef <?= $chef->first_name ?> sent you a message!</h2></td>
    </tr>

    <tr>
        <td>
            <p>You have a new message from Chef <?= $chef->first_name ?> about your upcoming
                meal reservation on <?= ConversionCalculationHelper::getMonthName($meal->meal_date) . ' ' . ConversionCalculationHelper::getDay($meal->meal_date); ?>. Open the Alfred app to check
                it out.</p>
        </td>
    </tr>

    <tr>
        <td style="text-align:center;">
            <a href="#" class="btn">
                View message
            </a>
        </td>
    </tr>

    <tr>
        <td>
            <p>Question? Call us 516 387 4362 or reply to this email.</p>
            <p>- The Alfred team</p>
        </td>
    </tr>

</table>
