<?php
//die('heyy');
session_start();
$data = (file_get_contents('php://input'));
$data = json_decode($data, true);

$api_url = $data['api_url'];
$request = $data['req'];

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $request,
    CURLOPT_HTTPHEADER => array(
        "accept: application/xml",
        "content-type: text/xml",
        "soapAction: http://tempuri.org/IService1/getBureauCreditReportV4"
    ),
));
$xml = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);
//print_r($xml); die();echo 'curl';
//print_r($error);die('request');
echo $xml;
//return json_encode($xml);
?>
