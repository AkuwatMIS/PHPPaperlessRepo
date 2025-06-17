<?php
/**
 * Created by PhpStorm.
 * User: DELl
 * Date: 2/4/2017
 * Time: 2:59 PM
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
        <td><h2>Your payment is on the way!</h2></td>
    </tr>

    <tr>
        <td>
            <p>We hope you had a fantastic experience cooking yesterday! Your
                payment of $<?= ConversionCalculationHelper::getTotalAmountAfterDeduction($meal->meal_totalcost) ?> is on the way! It will be deposited directly into
                your bank account.</p>
        </td>
    </tr>

    <tr>
        <td>
            <p><a href="#">
                    Now login to Alfred and reply to more meal requests!
                </a>
            </p>
        </td>
    </tr>

    <tr>
        <td>
            <p>Question? Call us 516 387 4362 or reply to this email.</p>
            <p>- The Alfred team</p>
        </td>
    </tr>

</table>
