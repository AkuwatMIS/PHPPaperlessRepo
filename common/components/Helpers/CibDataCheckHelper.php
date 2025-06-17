<?php

namespace common\components\Helpers;

use common\models\CibTypes;
use common\models\MembersPhone;

class CibDataCheckHelper
{
    public static function getReportold($type, $body)
    {
        $curl = curl_init();
        $cred_model = CibTypes::find()->where(['name' => $type])->one();
        $_url=/*'url_'.*/$cred_model->environment;
        $api_url=$cred_model->url_live.'/WCFDataCheckEnquiry/Service1.svc';
        //$api_url=$cred_model->$_url.'/WCFDataCheckEnquiry/Service1.svc';
        $checkerName = $cred_model->username;
        $makerCheckerPass = $cred_model->password;
        $access_token = $cred_model->auth_token;
        $memberCode = $cred_model->api_key_1;
        $controlBranchCode = $cred_model->api_key_2;
        $appId = $body['ApplicationId'];
        //$cnic = str_replace("-","", $body['CNIC']);
        $cnic = str_replace("-","", "35202-9256038-9");
        $nameArray = explode(" ",$body['fullName']);
        //$firstName = $body['fullName'];
        $firstName = $nameArray[0];
        //$middleName = $nameArray[1];
        $parentage = $nameArray[2];
        $dob = $body['dateOfBirth'];
//      $parentage = $body['fatherHusbandName'];
        $city = $body['city'];
        $amount = $body['loanAmount'];
        $gender = $body['gender'];
        $address = $body['currentAddress'];
        $phone = $body['phone'];
        $group_type = $body['groupType'];

        $request = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:tem=\"http://tempuri.org/\" xmlns:dat=\"http://schemas.datacontract.org/2004/07/DataCheckEnquiry\">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:getBureauCreditReportV3_1>
         <!--Optional:-->
         <tem:authKey>$access_token</tem:authKey>
         <!--Optional:-->
         <tem:cr>
            <!--Optional:-->
            <dat:CO_ASSOCIATION_1></dat:CO_ASSOCIATION_1>
            <!--Optional:-->
            <dat:CO_ASSOCIATION_2></dat:CO_ASSOCIATION_2>
            <!--Optional:-->
            <dat:CO_ASSOCIATION_3></dat:CO_ASSOCIATION_3>
            <!--Optional:-->
            <dat:CO_ASSOCIATION_4></dat:CO_ASSOCIATION_4>
            <!--Optional:-->
            <dat:CO_ASSOCIATION_5></dat:CO_ASSOCIATION_5>
            <!--Optional:-->
            <dat:CO_CNIC_1></dat:CO_CNIC_1>
            <!--Optional:-->
            <dat:CO_CNIC_2></dat:CO_CNIC_2>
            <!--Optional:-->
            <dat:CO_CNIC_3></dat:CO_CNIC_3>
            <!--Optional:-->
            <dat:CO_CNIC_4></dat:CO_CNIC_4>
            <!--Optional:-->
            <dat:CO_CNIC_5></dat:CO_CNIC_5>
            <!--Optional:-->
            <dat:CO_FIRST_NAME_1></dat:CO_FIRST_NAME_1>
            <!--Optional:-->
            <dat:CO_FIRST_NAME_2></dat:CO_FIRST_NAME_2>
            <!--Optional:-->
            <dat:CO_FIRST_NAME_3></dat:CO_FIRST_NAME_3>
            <!--Optional:-->
            <dat:CO_FIRST_NAME_4></dat:CO_FIRST_NAME_4>
            <!--Optional:-->
            <dat:CO_FIRST_NAME_5></dat:CO_FIRST_NAME_5>
            <!--Optional:-->
            <dat:CO_LAST_NAME_1></dat:CO_LAST_NAME_1>
            <!--Optional:-->
            <dat:CO_LAST_NAME_2></dat:CO_LAST_NAME_2>
            <!--Optional:-->
            <dat:CO_LAST_NAME_3></dat:CO_LAST_NAME_3>
            <!--Optional:-->
            <dat:CO_LAST_NAME_4></dat:CO_LAST_NAME_4>
            <!--Optional:-->
            <dat:CO_LAST_NAME_5></dat:CO_LAST_NAME_5>
            <!--Optional:-->
            <dat:CO_MID_NAME_1></dat:CO_MID_NAME_1>
            <!--Optional:-->
            <dat:CO_MID_NAME_2></dat:CO_MID_NAME_2>
            <!--Optional:-->
            <dat:CO_MID_NAME_3></dat:CO_MID_NAME_3>
            <!--Optional:-->
            <dat:CO_MID_NAME_4></dat:CO_MID_NAME_4>
            <!--Optional:-->
            <dat:CO_MID_NAME_5></dat:CO_MID_NAME_5>
            <!--Optional:-->
            <dat:CO_NIC_1></dat:CO_NIC_1>
            <!--Optional:-->
            <dat:CO_NIC_2></dat:CO_NIC_2>
            <!--Optional:-->
            <dat:CO_NIC_3></dat:CO_NIC_3>
            <!--Optional:-->
            <dat:CO_NIC_4></dat:CO_NIC_4>
            <!--Optional:-->
            <dat:CO_NIC_5></dat:CO_NIC_5>
            <!--Optional:-->
            <dat:Designation></dat:Designation>
            <!--Optional:-->
            <dat:GroupId></dat:GroupId>
            <!--Optional:-->
            <dat:SelfEmployed></dat:SelfEmployed>
            <!--Optional:-->
            <dat:TransactionNum></dat:TransactionNum>
            <!--Optional:-->
            <dat:accountType>IN</dat:accountType>
            <!--Optional:-->
            <dat:address>$address</dat:address>
            <!--Optional:-->
            <dat:amount>$amount</dat:amount>
            <!--Optional:-->
            <dat:applicationId>$appId</dat:applicationId>
            <!--Optional:-->
            <dat:associationType>PRN</dat:associationType>
            <!--Optional:-->
            <dat:cellNo>$phone</dat:cellNo>
            <!--Optional:-->
            <dat:checkerPassword>$makerCheckerPass</dat:checkerPassword>
            <!--Optional:-->
            <dat:checkerUserName>$checkerName</dat:checkerUserName>
            <!--Optional:-->
            <dat:cityOrDistrict>$city</dat:cityOrDistrict>
            <!--Optional:-->
            <dat:cnicNo>$cnic</dat:cnicNo>
            <!--Optional:-->
            <dat:controlBranchCode>$controlBranchCode</dat:controlBranchCode>
            <!--Optional:-->
            <dat:dateOfBirth>$dob</dat:dateOfBirth>
            <!--Optional:-->
            <dat:dateofapplication></dat:dateofapplication>
            <!--Optional:-->
            <dat:dependants></dat:dependants>
            <!--Optional:-->
            <dat:employerAddress></dat:employerAddress>
            <!--Optional:-->
            <dat:employerCellNo></dat:employerCellNo>
            <!--Optional:-->
            <dat:employerCityOrDistrict></dat:employerCityOrDistrict>
            <!--Optional:-->
            <dat:employerCompanyName></dat:employerCompanyName>
            <!--Optional:-->
            <dat:employerPhoneNo></dat:employerPhoneNo>
            <!--Optional:-->
            <dat:enquirystatus></dat:enquirystatus>
            <!--Optional:-->
            <dat:fatherOrHusbandFirstName></dat:fatherOrHusbandFirstName>
            <!--Optional:-->
            <dat:fatherOrHusbandLastName></dat:fatherOrHusbandLastName>
            <!--Optional:-->
            <dat:fatherOrHusbandMiddleName></dat:fatherOrHusbandMiddleName>
            <!--Optional:-->
            <dat:firstName>$firstName</dat:firstName>
            <!--Optional:-->
            <dat:gender>$gender</dat:gender>
            <!--Optional:-->
            <dat:lastName>$parentage</dat:lastName>
            <!--Optional:-->
            <dat:makerPassword>$makerCheckerPass</dat:makerPassword>
            <!--Optional:-->
            <dat:makerUserName>MAK1</dat:makerUserName>
            <!--Optional:-->
            <dat:maritialstatus></dat:maritialstatus>
            <!--Optional:-->
            <dat:memberCode>$memberCode</dat:memberCode>
            <!--Optional:-->
            <dat:middleName></dat:middleName>
            <!--Optional:-->
            <dat:nationality></dat:nationality>
            <!--Optional:-->
            <dat:nationalitytype></dat:nationalitytype>
            <!--Optional:-->
            <dat:nicNoOrPassportNo></dat:nicNoOrPassportNo>
            <!--Optional:-->
            <dat:ntnno></dat:ntnno>
            <!--Optional:-->
            <dat:permanentaddress>$address</dat:permanentaddress>
            <!--Optional:-->
            <dat:permanentcity></dat:permanentcity>
            <!--Optional:-->
            <dat:phoneNo></dat:phoneNo>
            <!--Optional:-->
            <dat:profession></dat:profession>
            <!--Optional:-->
            <dat:qualification></dat:qualification>
            <!--Optional:-->
            <dat:subBranchCode>0001</dat:subBranchCode>
            <!--Optional:-->
            <dat:title></dat:title>
         </tem:cr>
      </tem:getBureauCreditReportV3_1>
   </soapenv:Body>
</soapenv:Envelope>";

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
                "soapAction: http://tempuri.org/IService1/getBureauCreditReportV3_1"
            ),
        ));

        $xml = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        return self::parseXml($xml);
    }

    //version 4
    public static function getReport($type, $body)
    {
        $curl = curl_init();
        $cred_model = CibTypes::find()->where(['name' => $type])->one();

        $_url=/*'url_'.*/$cred_model->environment;
        $api_url=$cred_model->url_live.'/WCFDataCheckEnquiry/Service1.svc';
        //$api_url=$cred_model->$_url.'/WCFDataCheckEnquiry/Service1.svc';
        //print_r($cred_model);die();
        $checkerName = $cred_model->username;
        $makerCheckerPass = $cred_model->password;
        $access_token = $cred_model->auth_token;
        $memberCode = $cred_model->api_key_1;
        $controlBranchCode = $cred_model->api_key_2;
        //$appId = 2;
        $appId = $body['applicationId'].date('YmdHis');
        $cnic = str_replace("-","", $body['cnicNo']);

        $fullName = $body['firstName'];
        $firstName = '';
        $middleName = '';
        $lastName = '';

//        $nameArray = explode(" ",$body['firstName']);
//        $firstName = isset($nameArray[0]) && !empty($nameArray[0])?$nameArray[0]:'';
//        $middleName = isset($nameArray[1]) && !empty($nameArray[1])?$nameArray[1]:'';
//        $lastName = isset($nameArray[2]) && !empty($nameArray[2])?$nameArray[2]:'';

        $dob = $body['dateOfBirth'];
        $parentage = $body['fatherOrHusbandFirstName'];
        $parentageFirstName = $parentage;
        $parentageMiddleName = '';
        $parentageLastName = '';

        $parentageArray = explode(" ",$parentage);

//        $parentageFirstName = isset($parentageArray[0]) && !empty($parentageArray[0])?$parentageArray[0]:'';
//        $parentageMiddleName = isset($parentageArray[1]) && !empty($parentageArray[1])?$parentageArray[1]:'';
//        $parentageLastName = isset($parentageArray[2]) && !empty($parentageArray[2])?$parentageArray[2]:'';

        $city = $body['cityOrDistrict'];
        $amount = $body['amount'];
        $gender = $body['gender'];
        $address = $body['address'];
        $phone = $body['phoneNo'];
        $group_type = $body['groupType'];

        $request = "
        <s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">
            <s:Body>
                <getBureauCreditReportV4 xmlns=\"http://tempuri.org/\">
                    <authKey>$access_token</authKey>
                    <cr xmlns:d4p1=\"http://schemas.datacontract.org/2004/07/DataCheckEnquiry\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">
                    <d4p1:CO_ASSOCIATION_1 i:nil=\"true\" />
                    <d4p1:CO_ASSOCIATION_2 i:nil=\"true\" />
                    <d4p1:CO_ASSOCIATION_3 i:nil=\"true\" />
                    <d4p1:CO_ASSOCIATION_4 i:nil=\"true\" />
                    <d4p1:CO_ASSOCIATION_5 i:nil=\"true\" />
                    <d4p1:CO_CNIC_1 i:nil=\"true\" />
                    <d4p1:CO_CNIC_2 i:nil=\"true\" />
                    <d4p1:CO_CNIC_3 i:nil=\"true\" />
                    <d4p1:CO_CNIC_4 i:nil=\"true\" />
                    <d4p1:CO_CNIC_5 i:nil=\"true\" />
                    <d4p1:CO_FIRST_NAME_1 i:nil=\"true\" />
                    <d4p1:CO_FIRST_NAME_2 i:nil=\"true\" />
                    <d4p1:CO_FIRST_NAME_3 i:nil=\"true\" />
                    <d4p1:CO_FIRST_NAME_4 i:nil=\"true\" />
                    <d4p1:CO_FIRST_NAME_5 i:nil=\"true\" />
                    <d4p1:CO_LAST_NAME_1 i:nil=\"true\" />
                    <d4p1:CO_LAST_NAME_2 i:nil=\"true\" />
                    <d4p1:CO_LAST_NAME_3 i:nil=\"true\" />
                    <d4p1:CO_LAST_NAME_4 i:nil=\"true\" />
                    <d4p1:CO_LAST_NAME_5 i:nil=\"true\" />
                    <d4p1:CO_MID_NAME_1 i:nil=\"true\" />
                    <d4p1:CO_MID_NAME_2 i:nil=\"true\" />
                    <d4p1:CO_MID_NAME_3 i:nil=\"true\" />
                    <d4p1:CO_MID_NAME_4 i:nil=\"true\" />
                    <d4p1:CO_MID_NAME_5 i:nil=\"true\" />
                    <d4p1:CO_NIC_1 i:nil=\"true\" />
                    <d4p1:CO_NIC_2 i:nil=\"true\" />
                    <d4p1:CO_NIC_3 i:nil=\"true\" />
                    <d4p1:CO_NIC_4 i:nil=\"true\" />
                    <d4p1:CO_NIC_5 i:nil=\"true\" />
                    <d4p1:GroupId i:nil=\"true\" />
                    <d4p1:TransactionNum i:nil=\"true\" />
                    <d4p1:accountType>rm</d4p1:accountType>
                    <d4p1:address>$address</d4p1:address>
                    <d4p1:amount>$amount</d4p1:amount>
                    <d4p1:applicationId>$appId</d4p1:applicationId>
                    <d4p1:associationType>PRN</d4p1:associationType>
                    <d4p1:checkerPassword>$makerCheckerPass</d4p1:checkerPassword>
                    <d4p1:checkerUserName>$checkerName</d4p1:checkerUserName>
                    <d4p1:cityOrDistrict></d4p1:cityOrDistrict>
                    <d4p1:cnicNo>$cnic</d4p1:cnicNo>
                    <d4p1:controlBranchCode>$controlBranchCode</d4p1:controlBranchCode>
                    <d4p1:dateOfBirth>$dob</d4p1:dateOfBirth>
                    <d4p1:fatherOrHusbandFirstName>$parentageFirstName</d4p1:fatherOrHusbandFirstName>
                    <d4p1:fatherOrHusbandLastName>$parentageLastName</d4p1:fatherOrHusbandLastName>
                    <d4p1:fatherOrHusbandMiddleName>$parentageMiddleName</d4p1:fatherOrHusbandMiddleName>
                    <d4p1:firstName>$fullName</d4p1:firstName>
                    <d4p1:gender>$gender</d4p1:gender>
                    <d4p1:lastName>$lastName</d4p1:lastName>
                    <d4p1:makerPassword>$makerCheckerPass</d4p1:makerPassword>
                    <d4p1:makerUserName>mapi</d4p1:makerUserName>
                    <d4p1:memberCode>$memberCode</d4p1:memberCode>
                    <d4p1:middleName>$middleName</d4p1:middleName>
                    <d4p1:phoneNo i:nil=\"true\" />
                    <d4p1:subBranchCode>1202</d4p1:subBranchCode>
                    </cr>
                </getBureauCreditReportV4>
            </s:Body>
        </s:Envelope>";

        $headers = array
        (
            //'Content-Type: application/json',
            'Authorization: '.$access_token,
        );
        $array['api_url'] = $api_url;
        $array['req'] = $request;
        //$response = NetworkHelper::curlCall('http://10.84.15.50/cib/datacheck/report.php', $headers ,$array, true);
//        $response = NetworkHelper::curlCall('http://116.71.135.115/cib/datacheck/report.php', $headers ,$array, true);
        $response = NetworkHelper::curlCall('http://116.58.62.238/cib/datacheck/report.php', $headers ,$array, true);
        //print_r($response);die('hii-ll');
        /*if ($response->statusCode == '111') {
            $res['success'] = true;
            $res['data'] = $response->data;
            return $res;
        } else {
            $res['success'] = false;
            //$res['data'] = $response->statusCode;
            $res['data'] = $response;
            return $res;
        }*/
        /*curl_setopt_array($curl, array(
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
        print_r($xml);echo 'curl';
        print_r($error);die('request');*/
        return self::parseXml($response);
    }

    public static function getChangePassword($type)
    {
        $curl = curl_init();
        $cred_model = CibTypes::find()->where(['name' => $type])->one();
        $_url='url_'.$cred_model->environment;
        $api_url=$cred_model->$_url.'/WCFDataCheckEnquiry/Service1.svc';
        $makerChecker = $cred_model->username;
        $makerOldPass = $cred_model->password;
        $makerNewPass = 'Akhuwat@6666';
        $auth_token = $cred_model->auth_token;
        $memberCode = $cred_model->api_key_1;
        $controlBranchCode = $cred_model->api_key_2;

        $request = " <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:tem=\"http://tempuri.org/\" xmlns:dat=\"http://schemas.datacontract.org/2004/07/DataCheckEnquiry\">
        <soapenv:Header/>
           <soapenv:Body>
              <tem:ChangePassword>
                <tem:authKey>$auth_token</tem:authKey>
                 <tem:pc>
                    <dat:controlBranchCode>$controlBranchCode</dat:controlBranchCode>
                    <dat:makerchecker>$makerChecker</dat:makerchecker>
                    <dat:memberCode>$memberCode</dat:memberCode>
                    <dat:newpass>$makerNewPass</dat:newpass>
                    <dat:oldpass>$makerOldPass</dat:oldpass>
                 </tem:pc>
              </tem:ChangePassword>
            </soapenv:Body>
        </soapenv:Envelope>";

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
                "soapAction: http://tempuri.org/IService1/ChangePassword"
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $cred_model->password = $makerNewPass;
            if($cred_model->save()){
                return self::parseXml($xml);
            }
        }
    }

    public static function getExpiryPassword($type)
    {
        $curl = curl_init();

        $cred_model = CibTypes::find()->where(['name' => $type])->one();
        $_url='url_'.$cred_model->environment;
        $api_url=$cred_model->$_url.'/WCFDataCheckEnquiry/Service1.svc';
        $makerChecker = $cred_model->username;
        $auth_token = $cred_model->auth_token;
        $memberCode = $cred_model->api_key_1;
        $controlBranchCode = $cred_model->api_key_2;

        $request = "
       <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:tem=\"http://tempuri.org/\" xmlns:dat=\"http://schemas.datacontract.org/2004/07/DataCheckEnquiry\">
           <soapenv:Header/>
           <soapenv:Body>
              <tem:CheckPasswordExpiry>
                 <!--Optional:-->
                 <tem:authKey>$auth_token</tem:authKey>
                 <!--Optional:-->
                 <tem:pe>
                    <!--Optional:-->
                    <dat:controlBranchCode>$controlBranchCode</dat:controlBranchCode>
                    <!--Optional:-->
                    <dat:makerchecker>$makerChecker</dat:makerchecker>
                    <!--Optional:-->
                    <dat:memberCode>$memberCode</dat:memberCode>
                 </tem:pe>
              </tem:CheckPasswordExpiry>
           </soapenv:Body>
        </soapenv:Envelope>
        ";

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
                "soapAction: http://tempuri.org/IService1/CheckPasswordExpiry"
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return self::parseXml($xml);
        }
    }

    private static function parseXml($xml)
    {
        $parser = xml_parser_create('ISO-8859-1');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $values);
        xml_parser_free($parser);

        $return = array();
        $stack = array();
        foreach ($values as $val) {
            if ($val['type'] == "open") {
                array_push($stack, $val['tag']);
            } elseif ($val['type'] == "close") {
                array_pop($stack);
            } elseif ($val['type'] == "complete") {
                array_push($stack, $val['tag']);
                self::setArrayValue($return, $stack, $val['value']);
                array_pop($stack);
            }
        }

        return $return;
    }

    private static function setArrayValue(&$array, $stack, $value) {
        if ($stack) {
            $key = array_shift($stack);
            self::setArrayValue($array[$key], $stack, $value);
            return $array;
        } else {
            $array = $value;
        }
    }

    public static function parseRequestBody($application)
    {
        $phone = MembersPhone::find()->where(['member_id'=>$application->member_id])
            ->andWhere(['phone_type'=>'Mobile'])
            ->andWhere(['is_current'=>1])
            ->one();
        $arr = [
            "applicationId" => isset($application->application_no) ? str_replace("-", "", $application->id) : '',
            "cnicNo" => isset($application->member->cnic) ? str_replace("-", "", $application->member->cnic) : '',
            "firstName" => isset($application->member->full_name) ? $application->member->full_name : '',
            "dateOfBirth" => isset($application->member->dob) ? date('d-m-Y', $application->member->dob) : '01-jan-1970',
            "cityOrDistrict" => isset($application->branch->district->name) ? $application->branch->district->name : '',
            "amount" => isset($application->req_amount) ? '' . round($application->req_amount) . '' : '0',
            "gender" => isset($application->member->gender) ? ($application->member->gender) : '',
            "address" => isset($application->member->businessAddress->address) ? $application->member->businessAddress->address : '',
            "fatherOrHusbandFirstName" => isset($application->member->parentage) ? $application->member->parentage : '',
            "groupType" => isset($application->group->grp_type) ? $application->group->grp_type : '',
            "phoneNo" => isset($phone->phone) ? $phone->phone : ''
        ];
        //print_r($arr);die();
        return $arr;
    }

}