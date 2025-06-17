<?php
include('connection.php');
$requests = $conn->query('select * from cib_requests where status=0')->fetchAll();
///Start LOGIN CALL///
$body = [
    "UserName" => 'akhuwat.api',
    "Password" => 'Pakistan@1234',
];
$headers = array
(
    'Content-Type: application/json',
);
$url = 'https://cib.tasdeeq.com:8888/' . 'Authenticate/';
$ch = curl_init($url);
$type = true;
curl_setopt($ch, CURLOPT_POST, $type);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
$response = json_decode($result);
if (!isset($response->data->auth_token)) {
    die('Login Not Successfull!');
} else {
    $auth_token = $response->data->auth_token;
      ///END; LGON CALL///
    foreach ($requests as $request) {
        ///Start Get Report Call
        $headers = array
        (
            'Content-Type: application/json',
            'Authorization: bearer ' . $auth_token,
        );
        $body['reportDataObj'] =[
            "CNIC"=>$request['cnic'],
            "fullName"=>$request['full_name'],
            //"dateOfBirth"=>$request['dateOfBirth'],
            "loanAmount"=>$request['requested_amount'],
            "gender"=>$request['gender'],
            "currentAddress"=>$request['address']
        ];
        $url='https://cib.tasdeeq.com:8888/CreditInformationReportUpdated/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if(curl_exec($ch) === false)
        {
            echo 'Curl error: ' . curl_error($ch);
        }

        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result);
        /// END GET REPORT CALL
        $res=[];
        if ($response->statusCode == '111') {
            $res = json_encode($response->data);
        } else {
            $res= $response->statusCode;
        }
        $data = [
            'response' => $res,
            'status' => 1,
            'id' => $request['id']
        ];

        $sql = "UPDATE cib_requests SET response=:response, status=:status where id=:id";
        $stmt= $conn->prepare($sql);
        $stmt->execute($data);
    }
}
?>
