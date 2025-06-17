<?php
/**
 * Created by PhpStorm.
 * User: Junaid Fayyaz
 * Date: 9/9/2017
 * Time: 7:40 PM
 */

namespace common\components\Helpers;
use \Yii;

class FireBaseHelper
{
    public static function sendNotification($registrationIds,$message){
        // API access key from Google API's Console
        $api_access_key=Yii::$app->params['api_key'];
        // prep the bundle
        /*$msg = array
        (
            'message' 	=> $message['message'],
            'title'		=> $message['title'],
            'subtitle'	=> $message['subtitle'],
            'tickerText'	=> $message['tickerText'],
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon'
        );*/
        /*$fields = array
        (
            'registration_ids' 	=>$registrationIds,
            'data'			=> $msg
        );*/
       /* $data =  array
        (
            'action' 	=>  $message['action'],
            'info' => $message['info'],
        );
         $msg = array
        (
            'body' 	=> $message['message'],
            'title'	=> $message['title'],
            'counter' => 1,
            'vibrate' => 1,
            'sound' => 1,
            'icon'	=> 'http://beta.akhuwat.org.pk/uploads/noimage.png',
            'priority'	=> $message['priority'],
            'click_action'	=> $message['click_action'],
            'data' => $data,
        );*/
        $data =  array
        (
            'message' 	=> $message['message'],
            'title'	=> $message['title'],
            'counter' => 1,
            'vibrate' => 1,
            'sound' => 1,
            'icon'	=> 'http://beta.akhuwat.org.pk/uploads/noimage.png',/*Default Icon*/
            'priority'	=> $message['priority'],
            'click_action'	=> $message['click_action'],
            'action' 	=>  $message['action'],
            'info' => $message['info'],
        );
        $msg = array
        (
            'body' 	=> $message['message'],
            'title'	=> $message['title'],

        );

        $fields = array
        (
            //'to' => $registrationIds,
            'registration_ids'		=> $registrationIds,
            'notification'	=> $msg,
            'data' => $data,
        );

        /*print_r(json_encode($fields));
        die();*/
        $headers = array
        (
            'Authorization: key=' . $api_access_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        return $result;
    }
}