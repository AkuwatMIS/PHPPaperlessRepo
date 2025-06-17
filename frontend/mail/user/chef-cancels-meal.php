<?php
/**
 * Created by PhpStorm.
 * User: DELl
 * Date: 2/4/2017
 * Time: 2:54 PM
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
            <h2>Bad news—Chef <?= $chef->first_name ?> cancelled!</h2>
        </td>
    </tr>

    <tr>
        <td>
            <p>Chef <?= $chef->first_name ?> cancelled your <?= $meal->meal_type ?> reservation on <?= ConversionCalculationHelper::getMonthName($meal->meal_date) . ' ' . ConversionCalculationHelper::getDay($meal->meal_date); ?>
                at <?= $meal->time_from ?>. We are just as unhappy as you are. Create a new meal now
                and hopefully you can still keep your plans.</p>
        </td>
    </tr>

    <tr>
        <td style="text-align:center;">
            <a href="#" class="btn">Plan new meal</a>
        </td>
    </tr>

    <tr>
        <td>
            <p>Also consider <a href="#" style="text-decoration:underline;">rating Chef <?= $chef->first_name ?>.</a></p>

            <p>Question? Call us 516 387 4362 or reply to this email.</p>
            <p>- The Alfred team</p>
        </td>
    </tr>

</table>
