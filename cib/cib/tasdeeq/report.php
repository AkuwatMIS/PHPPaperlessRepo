<?php
session_start();
$data = (file_get_contents('php://input'));
$data = json_decode($data, true);
$access_token = apache_request_headers()['Authorization'];

$headers = array(
    'Authorization: Bearer '.$access_token,
    'Content-Type: Application/json',
);

$body['reportDataObj'] = [
    "CNIC" => $data['CNIC'],
    "fullName" => $data['fullName'],
    "dateOfBirth" => $data['dateOfBirth'],
    "city" => $data['city'],
    "loanAmount" => $data['loanAmount'],
    "gender" => $data['gender'],
    "currentAddress" => $data['currentAddress'],
    "fatherHusbandName" => $data['fatherHusbandName']
];

//$url=$data['url'];
//$url='https://cib.tasdeeq.com:8888/'.'TestCreditInformationReport/';
//$url='https://cib.tasdeeq.com:8888/CreditInformationReportUpdated/';
//$url = 'https://cib.tasdeeq.com:8888/CreditInformationReport//';
$url='https://cib.tasdeeq.com:8888/CreditInformationOrderedReportUpdated/';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($ch);

if ($result === false) {
    echo 'Curl error: ' . curl_error($ch);
}


curl_close($ch);
echo $result;
//return $result;
?>