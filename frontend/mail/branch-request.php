<?php
/**
 * Created by PhpStorm.
 * User: DELl
 * Date: 2/4/2017
 * Time: 1:23 PM
 */
use yii\helpers\Url;

?>

<table width="650" border="0" cellspacing="0" cellpadding="0" style="margin:20px auto;">
    <!--<tr>
        <td style="text-align:center;">
            <img src="http://credit.akhuwat.org.pk/images/logo.png" style="" alt="">
        </td>
    </tr>-->
    <tr>
        <td style="text-align: center" colspan="2"><h2>New Branch Information!</h2></td>
    </tr>
    <tr>
        <td style="text-align: left" colspan="2">
            <p>Dear colleagues,</p>
        </td>
    </tr>
    <tr>
        <td style="text-align: left " colspan="2">
            <p>Please note down the new branches information for your record, knowledge and necessary action.</p>
        </td>
    </tr>
    <tr>
        <td style="text-align: left">
            <b>Branch: </b><?= $message['name']?>
        </td>
        <td style="text-align: left">
            <b>Code: </b><?= $message['code']?>
        </td>
    </tr>
    <tr>
        <td style="text-align: left">
            <b>Division: </b><?= $message['division']?>
        </td>
        <td style="text-align: left">
            <b>Region: </b><?= $message['region']?>
        </td>
    </tr>
    <tr>
        <td style="text-align: left" colspan="2">
            <b>Area: </b><?= $message['area']?>
        </td>
    </tr>
    <tr>
        <td style="text-align: left" colspan="2">
            <b>Projects: </b><?= $message['projects']?>
        </td>
    </tr>
</table>