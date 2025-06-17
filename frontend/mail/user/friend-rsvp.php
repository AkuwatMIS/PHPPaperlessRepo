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
        <td>
            <h2><?= $user->first_name ?>, <?= $guest->full_name ?> RSVP’d to <?= $meal->meal_type ?>!</h2>
        </td>
    </tr>

    <tr>
        <td>
            <p><?= $guest->full_name ?> just RSVP’d for your <?= $meal->meal_type ?> reservation
                on <?= ConversionCalculationHelper::getMonthName($meal->meal_date) . ' ' . ConversionCalculationHelper::getDay($meal->meal_date); ?>. If you have more open seats, invite more guests through the Alfred app.</p>
        </td>
    </tr>

    <tr>
        <td style="text-align:center;"><a href="#" class="btn">Invite more guests</a></td>
    </tr>

    <tr>
        <td>
            <p>Question? Call us 516 387 4362 or reply to this email.</p>
            <p>- The Alfred team</p>
        </td>
    </tr>

</table>
