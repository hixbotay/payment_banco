<?php 
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BANCO_PATH',__DIR__);
define('JPATH_ROOT',dirname(dirname(dirname(__DIR__))));

include 'lib/soapclient.php';
include 'lib/jbpaymentlib.php';


$msg_id = gen_uuid();
$user_id = 'FLY001';
$username='ECN';
$entity_id='00588';
$source_id='FLY001';
$source='BANCO';
$token=getToken();
$password='password1';
$total = 1;
$email = 'alice.ngunga@bancoecnomico.ao';
$start_pay_date = (new DateTime())->format('Y-m-d');
$end_pay_date = (new DateTime('+1 days'))->format('Y-m-d');
$config = (object)['username'=>$username,'password'=>$password];

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pay="http://www.bancoeconomico.ao/xsd/paymentref">
'.getHeader($config).'
<soapenv:Body>
	<pay:PaymentRefCreateRequest>
	<pay:HEADER>
		<pay:SOURCE>'.$source.'</pay:SOURCE>
		<pay:MSGID>'.$msg_id.'</pay:MSGID>
		<pay:USERID>'.$user_id.'</pay:USERID>
		<pay:BRANCH>000</pay:BRANCH>
		<!--Optional:-->
		<pay:PASSWORD/>
		<pay:INVOKETIMESTAMP>'.(new DateTime())->format('Y-m-d\TH:i:s').'</pay:INVOKETIMESTAMP>
	</pay:HEADER>
	<pay:BODY>
		<pay:Payment>
			<pay:AUTHTOKEN>'.$token.'</pay:AUTHTOKEN>
			<pay:ENTITYID>'.$entity_id.'</pay:ENTITYID>
			<pay:PRODUCT_NO>1</pay:PRODUCT_NO>
			<pay:SOURCE_ID>'.$source_id.'</pay:SOURCE_ID>
			<!--Optional:-->
		   <pay:REFERENCE/>
		   <pay:AMOUNT>'.$total.'</pay:AMOUNT>
		   <!--Optional:-->
		   <pay:START_DATE>'.$start_pay_date.'</pay:START_DATE>
		   <!--Optional:-->
		   <pay:END_DATE>'.$end_pay_date.'</pay:END_DATE>
		   <!--Optional:-->
		   <pay:TAX_RATE>0</pay:TAX_RATE>
		   <!--Optional:-->
		   <pay:CUSTOMER_NAME/>
		   <!--Optional:-->
		   <pay:ADDRESS/>
		   <!--Optional:-->
		   <pay:TAX_ID/>
		   <!--Optional:-->
		   <pay:EMAIL>'.$email.'</pay:EMAIL>
		   <!--Optional:-->
		   <pay:PHONE_NUMBER/>
		</pay:Payment>
	</pay:BODY>
	</pay:PaymentRefCreateRequest>
</soapenv:Body>
</soapenv:Envelope>';


//$result = getSoapResult('importPayment',$xml);
//JbPaymentbancoLib::debug($client);
//wsdl 1
//way 1\
$wsdl =  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';

$wsdl = BANCO_PATH.'/wsdl/WSI_PaymentRefCreate.wsdl';
$wsdl_endpoint = 'https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate';
$action = 'WSI_PaymentRefCreate';
$client = new RemoteSoapClient($wsdl);
//$client->__setSoapHeaders($header); 
$result = $client->execute($wsdl_endpoint,$xml,$action);

//$functions = $client->__getFunctions ();
//debug($functions);die;
//$result = $client->__soapCall($action,$xml1);

debug($result->PaymentRefCreateResponse->BODY->Payment_Details->PAYMENT_ID);
die;

$xml_query = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pay="http://www.bancoeconomico.ao/xsd/paymentrefdetails">
   '.getHeader($config).'
   <soapenv:Body>
      <pay:PaymentRefDetailsQueryRequest>
          <pay:HEADER>
            <pay:SOURCE>BANCO</pay:SOURCE>
            <pay:MSGID>1e504bc7-8fdf-493e-abb5-e26871e11915</pay:MSGID>
            <pay:USERID>FLY001</pay:USERID>
            <pay:BRANCH>000</pay:BRANCH>
            <!--Optional:-->
            <pay:PASSWORD/>
            <pay:INVOKETIMESTAMP>'.(new DateTime())->format('Y-m-d\TH:i:s').'</pay:INVOKETIMESTAMP>
         </pay:HEADER>
         <pay:BODY>
            <pay:Payment>
               <pay:AUTHTOKEN>'.$token.'</pay:AUTHTOKEN>
               <pay:ENTITYID>00588</pay:ENTITYID>
               <!--Optional:-->
               <pay:PaymentIdList>
                  <!--Zero or more repetitions:-->
                  <pay:PAYMENT_ID>43404</pay:PAYMENT_ID>
               </pay:PaymentIdList>
               <!--Optional:-->
               <pay:SourcetIdList>
                  <!--Zero or more repetitions:-->
                  <pay:SOURCE_ID/>
               </pay:SourcetIdList>
            </pay:Payment>
         </pay:BODY>
      </pay:PaymentRefDetailsQueryRequest>
   </soapenv:Body>
</soapenv:Envelope>';


$wsdl = BANCO_PATH.'/wsdl/WSI_PaymentRefDetailsQuery.wsdl';
$wsdl_endpoint = 'https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery';
$action = 'WSI_PaymentRefDetailsQuery';
$client = new RemoteSoapClient($wsdl);
//$client->__setSoapHeaders($header); 
$result = $client->execute($wsdl_endpoint,$xml_query,$action);
debug($result->PAYMENT_ID);

die;
function getSoapResult($action,$xml){
	$wsdl = BANCO_PATH.'/lib/SPFService_v1.1.wsdl';	
	//$wsdl = 'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
	$client = new RemoteSoapClient($wsdl);

	//$functions = $client->__getFunctions ();
	//JbPaymentbancoLib::debug ($functions);
	
	try{
		//$result = $client->__soapCall($action,$xml);
		$result = $client->execute('https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl',$xml,$action);
		JbPaymentbancoLib::debug($client);
		return $result;
		/*
		JbPaymentbancoLib::debug($result);
		$soapEnvelope = new SimpleXMLElement($result);
		$name_spaces = $soapEnvelope->getNamespaces(true);
		$namespace = isset($name_spaces['soap-env']) ? $name_spaces['soap-env'] : $name_spaces['SOAP-ENV'];
		$result=  $soapEnvelope->children($namespace);
		JbPaymentbancoLib::debug($namespace);
		return $result;
		*/
		
	}catch(Exception $e){
		JbPaymentbancoLib::debug($e->getMessage());
		return false;
	}
	return false;
}

function gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
}
	
function getToken(){
	$token = 'MTU2NzE3OTg1MDk4Njo0ZjFiNjA0MzE4ODYxODc3ODgzYjVkYmUyNzAzYWY0NGM5ZGRlOWI3NzBkNTc2MzdmODI0NzM0NmYwNzQzNmYxYTczYTY2MzlmNTQ4Mjk3MTY4NTBjOTMxNmU2MDg3YjAzNWJiNWVhMTk0NDZiZmYxMGJjZGE5ODhjMzhmNWEyODc2ZGM4MjE0ZmFiOGM0NWViZDFlZjg4OWM2MmI1MDY1YzVhZjMwYzM2NjczM2U4Y2VmY2IyZTUxOTNjMWIyMjA4YTc0M2Y1ZTRhMzk4ZGU4NTc2YmU4ODBmMjZkM2I2MTZjNmNhZjJhYTNhNTY5NDIwYWNlMGZlOTUwZTc1Mjc3NzE1NTBkYjNiYjI5ODMzM2E5NzRmMzNkNGZmNTgwMDc4N2I5NDM1YmU0ZjFlZTMzNWJjMDIzZjc4YzlmZDA2NDliN2NjNjIwYWNlMmI3YzZkMmU1ZTM4ZWNhNmVkZmVhODI5NTM2ODBiYWFmMDAxNmRlNTY1NTRjNjEyMTJmZDNiODE0Nzk0YTZlZjk5N2RlNjAxMDI0YTEzYjJjNWM3OTFiZjlkNTU0ODZkNTdhYTk5OTEwNzc0MmY4ZmUyZDg0YmQ1ZjA1ZWNlMjAxMmIyYjFhYmNhMGIwNGFjYTNjZDY0YjA5MWZlYjdlMDc3ZGMzYTkzMjg4ODVmZTZlOWRmODowMDU4OF8xOmViNjYwNjIwMTU2Yjg0ZjdiMTAzZWQzMWM3MWYzMzQ3NGM5MTBiOThjODRlOWNjNmJlMWZjMGZkMDBiY2E0Nzg3ZTlmNDc5MGU3NjU4MmVhM2MxNjFmMWFhNzgxMGNkOTI2MTVhMjhkNjZhMTQwN2FlZWMyN2VjMGVhNzdjMGUx';
	return $token;
}

function getHeader($config){
	return '<soapenv:Header>	
	<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	<wsse:UsernameToken wsu:Id="soaAuth">
	<wsse:Username>'.$config->username.'</wsse:Username>	
	<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$config->password.'</wsse:Password>
	<wsu:Created>'.(new DateTime())->format('Y-m-d\TH:i:s').'</wsu:Created>
	</wsse:UsernameToken>
	</wsse:Security>
</soapenv:Header>';
}

