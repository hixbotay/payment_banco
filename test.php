<?php 
ini_set('soap.wsdl_cache_enabled', 0);
define('BANCO_PATH',__DIR__);

include 'lib/soapclient.php';
include 'lib/jbpaymentlib.php';
$endpoint = 'http://bancoeconomico.ao/economiconet/spfService';

$msg_id = gen_uuid();
$user_id = 'FLY001';
$username='FLY';
$entity_id='00588';
$payment_id='10000';
$source_id='FLY';
$token=getToken();
$password='passwordFLY1';


$xml = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pay="http://www.bancoeconomico.ao/xsd/paymentrefdetails">
<soapenv:Header>
	<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecuritysecext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
	<wsse:UsernameToken wsu:Id="soaAuth">
	<wsse:Username>'.$username.'</wsse:Username>
	<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-tokenprofile-1.0#PasswordText">'.$password.'</wsse:Password>
	<wsu:Created>'.(new DateTime())->format('Y-m-d H:i:s').'</wsu:Created>
	</wsse:UsernameToken>
	</wsse:Security>
</soapenv:Header>
<soapenv:Body>
	<pay:PaymentRefDetailsQueryRequest>
	<pay:HEADER>
		<pay:SOURCE>ECN</pay:SOURCE>
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
			<!--Optional:-->
			<pay:PaymentIdList>
			<!--Zero or more repetitions:-->
			<pay:PAYMENT_ID>'.$payment_id.'</pay:PAYMENT_ID>
			</pay:PaymentIdList>
			<!--Optional:-->
			<pay:SourcetIdList>
			<!--1 or more repetitions:-->
			<pay:SOURCE_ID>'.$source_id.'</pay:SOURCE_ID>
			</pay:SourcetIdList>
		</pay:Payment>
	</pay:BODY>
	</pay:PaymentRefDetailsQueryRequest>
</soapenv:Body>
</soapenv:Envelope>';

$xml1 = array(
'authToken' => 'YYYYYYY',
'entityId' => '12345',
'productNumber' => '1',
'sourceId' => 'A12345',
'amount' => '1234.56',
);

$result = getSoapResult('importPayment',$xml);
//JbPaymentbancoLib::debug($client);
JbPaymentbancoLib::debug($result);


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
	
}