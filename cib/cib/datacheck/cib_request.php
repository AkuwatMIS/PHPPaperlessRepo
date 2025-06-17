<?php
$data = file_get_contents('php://input');
$data=json_decode($data,true);
$headers = array
(
    'Content-Type: application/json',
    'Authorization: ' . '2ZPXSOkcDv9hRJJapY4E7xI27QaiSv',
);
$body['reportDataObj'] =$data['request_body'];;
//$url=$data['url'];
$url='https://cib.tasdeeq.com:8888/'.'TestCreditInformationReport/';
//$url='https://cib.tasdeeq.com:8888/CustomCreditInformationReportUpdated/';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
echo $result;
?>