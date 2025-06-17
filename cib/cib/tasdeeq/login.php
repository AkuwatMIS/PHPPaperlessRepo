<?php
/*
$data = file_get_contents('php://input');
$data=json_decode($data,true);
$headers = array
(
    'Content-Type: application/json',
);

$url=$data['url'];
$body = [
    "UserName" => $data['username'],
    "Password" => $data['password'],
];
//echo $url;
//echo json_encode($body);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if(curl_exec($ch) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
else
{
    echo 'Operation completed without any errors';
}
curl_close($ch);
print_r(json_decode($result));
die('aaaa');
//die('here')*/

session_start();
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$body = [
    "UserName" => $data['UserName'],
    "Password" => $data['Password'],
];
$headers = array
(
    'Content-Type: application/json',
);
$url = $data['url'];

$ch = curl_init();
$type = true;
$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);

if ($result === false) {
    echo 'Curl error: ' . curl_error($ch);
}
curl_close($ch);
//$response = json_decode($result);
echo $result;
//return $result;
?>