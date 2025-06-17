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
        <td><h2>Tell us about your experience!</h2></td>
    </tr>

    <tr>
        <td>
            <p>We hope your <?= $meal->meal_type ?> experience with Chef <?= $chef->first_name ?> was fantastic.
                Your final payment of
                $<?= ConversionCalculationHelper::getTotalAmountAfterDeduction($meal->meal_totalcost) ?> was
                successfully processed to your credit card ending in <?= substr($user->cc_no, -4, 4)?>. </p>

            <p>Alfred is a community built on trust. Please take a moment to
                review Chef <?= $chef->first_name ?> to improve your future experience and the
                experience of others.</p>
        </td>
    </tr>

    <tr>
        <td style="text-align:center;">
            <a href="#" class="btn">
                Rate Chef <?= $chef->first_name ?>
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
