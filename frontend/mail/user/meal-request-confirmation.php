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
        <td><h2><?= $user->first_name ?>, your meal request was sent out!</h2></td>
    </tr>

    <tr>
        <td>
            <p>Your <?= $meal->meal_type ?> request for <?= ConversionCalculationHelper::getMonthName($meal->meal_date) . ' ' . ConversionCalculationHelper::getDay($meal->meal_date); ?>
                at <?= $meal->time_from ?> has been sent out.</p>

            <p>Here’s what you can expect next:</p>
            <ul>
                <li>Carefully vetted local Chefs will be notified of your meal.</li>

                <li> You’ll begin receiving proposals from the Chefs, each will
                    include a link to the Chef’s profile, meal details and pricing.
                </li>

                <li> Review and compare your options. Then pick your favorite,
                    pay a thirty percent deposit for ingredients and sit back and
                    enjoy.
                </li>
            </ul>
        </td>
    </tr>

    <tr>
        <td>
            <p>Question? Call us 516 387 4362 or reply to this email.</p>
            <p>- The Alfred team</p>
        </td>
    </tr>

</table>
