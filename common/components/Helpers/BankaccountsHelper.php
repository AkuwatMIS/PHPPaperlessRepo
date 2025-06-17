<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 1/23/2020
 * Time: 3:50 PM
 */

namespace common\components\Helpers;


use common\models\Banks;

class BankaccountsHelper
{

    public static function getApiKeys($bank,$env) {
        $bank_details = Banks::find()->where(['bank_name' => $bank, 'environment' => $env]) ->one();
        return $bank_details;
    }

    public static function ablEcommereceApi($access_token,$request_id,$credit_account,$amount,$env) {
        $curl = curl_init();
        $api_credentials = self::getApiKeys('ABL',$env);
        $client_id = $api_credentials->api_key_1;
        //$access_token = 'AAIkMjUzMzRiYmMtMTQ4My00ZDY5LWIwZWUtOTJlMjhiYzNjN2U4A4aXfJMpAATBCr1dJJXDAbMso6Ebg7zFdCz8r89E2zMPPobNwa7CSKOHCxjw5r4VCH_qWiBHz9T9CIr0_VKaoPo6Q5QYBLXinI0-u3U-RlYlheqPYrxtg39C96N3U7bH0fBcSz0eoMe_-CZYT9mqMQ';

        $system_name = 'Akhuwat';
        $api_client_id = $api_credentials->api_key_3;
        $agent_id = $api_credentials->api_key_4;
        //$credit_account = '0010000108290010';
       // $amount = '100';
        $request = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
         <soapenv:Header>
          <wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\">
           <wsse:UsernameToken>
            <wsse:Username></wsse:Username>
            <wsse:Password></wsse:Password>
            <wsse:Nonce EncodingType=\"string\"></wsse:Nonce>
            <wsu:Created></wsu:Created>
           </wsse:UsernameToken>
           <wsu:Timestamp wsu:Id=\"string\">
            <wsu:Created></wsu:Created>
            <wsu:Expires></wsu:Expires>
           </wsu:Timestamp>
          </wsse:Security>
         </soapenv:Header>
         <soapenv:Body>
          <tns:NiftFundsTransfer xmlns:tns=\"http://ECommerceNiftWsdl\">
           <FundsTransferRequestHeader>
            <SystemName>$system_name</SystemName>
            <CorrelationID>$request_id</CorrelationID>
            <ClientID>$api_client_id</ClientID>
            <AgentID>$agent_id</AgentID>
           </FundsTransferRequestHeader>
           <FundsTransferRequestBody>
            <CreditAccount>$credit_account</CreditAccount>
            <DebitAmount>$amount</DebitAmount>
            <MappingID></MappingID>
            <Narration></Narration>
           </FundsTransferRequestBody>
          </tns:NiftFundsTransfer>
         </soapenv:Body>
        </soapenv:Envelope>";
        //$api_url = $api_credentials->base_url . '/ECommerceNiftWsdlHttpService';
        $api_url = $api_credentials->base_url . '/PayAnyone';
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
                "Authorization: Bearer ".$access_token,
                "X-IBM-Client-Id: ".$client_id
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        //$xml = htmlentities($xml);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $return = self::parseXml($xml);
            /*echo '<pre>';
            print_r($return);*/
            if(isset($return['errorResponse'])) {
                $token = self::getAccessToken($env);
                self::ablEcommereceApi($token,$request_id,$credit_account,$amount,$env);
                /*print_r($return['errorResponse']['httpCode']);
                print_r($return['errorResponse']['httpMessage']);
                print_r($return['errorResponse']['moreInformation']);*/

            } else {

                $response = $return['soapenv:Envelope']['soapenv:Body']['out2:NiftFundsTransferResponse']['FundsTransferResponse'];
                return $response;
                /*print_r($response['CorrelationID']);
                print_r($response['StatusCode']);
                print_r($response['StatusDescription']);*/
            }
        }

    }
    public static function ablEcommereceApiNew($access_token,$request_id,$credit_account,$amount,$env) {
        $curl = curl_init();
        $api_credentials = self::getApiKeys('ABL',$env);
        $client_id = $api_credentials->api_key_1;
        //$access_token = 'AAIkMjUzMzRiYmMtMTQ4My00ZDY5LWIwZWUtOTJlMjhiYzNjN2U4A4aXfJMpAATBCr1dJJXDAbMso6Ebg7zFdCz8r89E2zMPPobNwa7CSKOHCxjw5r4VCH_qWiBHz9T9CIr0_VKaoPo6Q5QYBLXinI0-u3U-RlYlheqPYrxtg39C96N3U7bH0fBcSz0eoMe_-CZYT9mqMQ';

        $system_name = 'Akhuwat';
        $api_client_id = $api_credentials->api_key_3;
        $agent_id = $api_credentials->api_key_4;
        //$credit_account = '0010000108290010';
        // $amount = '100';
        $request = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:apic=\"http://ApiConnectFTwsdlDefinition\">
   <soapenv:Header/>
   <soapenv:Body>
      <apic:FundsTransfer>
         <RequestHeader>
            <SystemName>$system_name</SystemName>
            <RequestID>$request_id</RequestID>
            <TenantID>abc11323===+</TenantID>
            <!--Optional:-->
            <Tag1>?</Tag1>
            <!--Optional:-->
            <Tag2>?</Tag2>
            <!--Optional:-->
            <Tag3>?</Tag3>
         </RequestHeader>
         <RequestBody>
            <CreditAccount>0010000108290010</CreditAccount>
            <DebitAccountNumber>0010043062000018</DebitAccountNumber>
            <DebitAmount>$amount</DebitAmount>
            <MappingID>456587</MappingID>
            <Narration>FT</Narration>
         </RequestBody>
      </apic:FundsTransfer>
   </soapenv:Body>
</soapenv:Envelope>
";
        $api_url = $api_credentials->base_url . '/ApiConnectFTwsdlDefinitionHttpService';
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
                "Authorization: Bearer ".$access_token,
                "X-IBM-Client-Id: ".$client_id
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        //$xml = htmlentities($xml);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $return = self::parseXml($xml);
            /*echo '<pre>';
            print_r($return);*/
            if(isset($return['errorResponse'])) {
                $token = self::getAccessToken($env);
                self::ablEcommereceApi($token,$request_id,$credit_account,$amount,$env);
                /*print_r($return['errorResponse']['httpCode']);
                print_r($return['errorResponse']['httpMessage']);
                print_r($return['errorResponse']['moreInformation']);*/

            } else {
                $response = $return['soapenv:Envelope']['soapenv:Body']['out3:FundsTransferResponse']['FundsTransferResponse'];
                return $response;
                /*print_r($response['CorrelationID']);
                print_r($response['StatusCode']);
                print_r($response['StatusDescription']);*/
            }
        }

    }
    public static function ablCheckAccountNo($access_token,$request_id,$credit_account,$amount,$env) {
        $curl = curl_init();
        $api_credentials = self::getApiKeys('ABL',$env);
        $client_id = $api_credentials->api_key_1;
        //$access_token = 'AAIkMjUzMzRiYmMtMTQ4My00ZDY5LWIwZWUtOTJlMjhiYzNjN2U4A4aXfJMpAATBCr1dJJXDAbMso6Ebg7zFdCz8r89E2zMPPobNwa7CSKOHCxjw5r4VCH_qWiBHz9T9CIr0_VKaoPo6Q5QYBLXinI0-u3U-RlYlheqPYrxtg39C96N3U7bH0fBcSz0eoMe_-CZYT9mqMQ';

        $system_name = 'Akhuwat';
        $api_client_id = $api_credentials->api_key_3;
        $agent_id = $api_credentials->api_key_4;
        //$credit_account = '0010000108290010';
        // $amount = '100';
        $request = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
 <soapenv:Header>
  <wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\" xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\">
   <wsse:UsernameToken>
    <wsse:Username>string</wsse:Username>
    <wsse:Password>string</wsse:Password>
    <wsse:Nonce EncodingType=\"string\">string</wsse:Nonce>
    <wsu:Created>string</wsu:Created>
   </wsse:UsernameToken>
   <wsu:Timestamp wsu:Id=\"string\">
    <wsu:Created>string</wsu:Created>
    <wsu:Expires>string</wsu:Expires>
   </wsu:Timestamp>
  </wsse:Security>
 </soapenv:Header>
 <soapenv:Body>
  <tns:AccountTitleFetch xmlns:tns=\"http://ApiConnectFTwsdlDefinition\"><!-- mandatory -->
   <RequestHeader><!-- mandatory -->
    <SystemName>$system_name</SystemName>
    <RequestID>$request_id</RequestID>
    <TenantID></TenantID>
    <Tag1>string</Tag1>
    <Tag2>string</Tag2>
    <Tag3>string</Tag3>
   </RequestHeader>
   <RequestBody><!-- mandatory -->
    <AccountNumber>$credit_account</AccountNumber>
   </RequestBody>
  </tns:AccountTitleFetch>
 </soapenv:Body>
</soapenv:Envelope>
";
        $api_url = $api_credentials->base_url . '/ApiConnectFTwsdlDefinitionHttpService';
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
                "Authorization: Bearer ".$access_token,
                "X-IBM-Client-Id: ".$client_id
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        //$xml = htmlentities($xml);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $return = self::parseXml($xml);
            /*echo '<pre>';
            print_r($return);*/
            if(isset($return['errorResponse'])) {
                $token = self::getAccessToken($env);
                self::ablEcommereceApi($token,$request_id,$credit_account,$amount,$env);
                /*print_r($return['errorResponse']['httpCode']);
                print_r($return['errorResponse']['httpMessage']);
                print_r($return['errorResponse']['moreInformation']);*/

            } else {
                $response = $return['soapenv:Envelope']['soapenv:Body']['out3:AccountTitleFetchResponse']['TitleFetchResponse'];
                return $response;
                /*print_r($response['CorrelationID']);
                print_r($response['StatusCode']);
                print_r($response['StatusDescription']);*/
            }
        }

    }
    public static function getAccessToken() {
        $env = 'live';
        $curl = curl_init();
        $api_credentials = self::getApiKeys('ABL',$env);
        $grant_type = 'client_credentials';
        $client_id = $api_credentials->api_key_1;
        $client_secret = $api_credentials->api_key_2;
        $api_url = $api_credentials->base_url . '/oauth2/token';
        $scope= 'ABLApis';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_ENCODING => "",
            CURLINFO_HEADER_OUT => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=".$grant_type."&client_id=".$client_id."&client_secret=".$client_secret."&scope=".$scope,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'content-type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = (array)json_decode($response);
            if(isset($response['access_token']))
            {
                return $response['access_token'];
            }
        }
    }

    private static function parseXml($xml) {
        $parser = xml_parser_create('ISO-8859-1');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $values);
        xml_parser_free($parser);

        $return = array();
        $stack = array();
        foreach($values as $val) {
            if($val['type'] == "open") {
                array_push($stack, $val['tag']);
            } elseif($val['type'] == "close") {
                array_pop($stack);
            } elseif($val['type'] == "complete") {
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
    public static function ablPayAnyOne($request,$member) {

    //public static function ablPayAnyOne($access_token,$request_id,$credit_account,$amount,$env,$member,$thirpartykey) {
        $curl = curl_init();
        $api_credentials    = self::getApiKeys('ABL','live');
        $client_id          = $api_credentials->api_key_1;
        $currency           ='PKR';
        $system_name        = 'Akhuwat';
        $api_client_id      =   $api_credentials->api_key_3;
        $agent_id           =   $api_credentials->api_key_4;
        $address            =   $member->businessAddress->address;
        $phone_no           =   $member->membersMobile->phone;
        $city               =   $member->branch->city->name;
        $request_id         =   $request['request_id'];
        $thirdpartykey      =   $request['thirdpartykey'];
        $amount             =   $request['amount'];
        $access_token             =   $request['access_token'];
        $request = "
        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
        <soapenv:Body>
        <tns:PayAnyone_Req xmlns:tns=\"http://PayAnyoneService\"><!-- mandatory -->
        <RequestId>$request_id</RequestId>

            <ClientId>$thirdpartykey</ClientId>

             <paymentType>C</paymentType>
        
             <payeeEmail>abk@abl.com</payeeEmail>
        
             <entity>$system_name</entity>
        
             <payeeCity>$city</payeeCity>
        
             <payeeName>$member->full_name</payeeName>
        
             <remitterName>$system_name</remitterName>
    
             <amount><!-- mandatory -->
        
             <amount>$amount</amount>
        
             <currency>$currency</currency>
        
             </amount>
        
             <payeeCNIC>$member->cnic</payeeCNIC>
        
             <payeeAddress>$address</payeeAddress>
    
            <payeeMobile>$phone_no</payeeMobile>
        </tns:PayAnyone_Req>
        </soapenv:Body>
        </soapenv:Envelope>
        ";

        $api_url = $api_credentials->base_url . '/PayAnyone';
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
                "Authorization: Bearer ".$access_token,
                "X-IBM-Client-Id: ".$client_id
            ),
        ));

        $xml = curl_exec($curl);
        $err = curl_error($curl);
        //$xml = htmlentities($xml);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $return = self::parseXml($xml);
            if(isset($return['errorResponse'])) {

                $token = self::getAccessToken();
                self::ablEcommereceApi($token,$request_id,$phone_no,$amount,'live');
                /*print_r($return['errorResponse']['httpCode']);
                print_r($return['errorResponse']['httpMessage']);
                print_r($return['errorResponse']['moreInformation']);*/

            } else {
                $response = $return['soapenv:Envelope']['soapenv:Body']['out2:PayAnyone_Res'];
                return $response;
                /*print_r($response['CorrelationID']);
                print_r($response['StatusCode']);
                print_r($response['StatusDescription']);*/
            }
        }

    }

}