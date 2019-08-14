<?php 
ini_set('soap.wsdl_cache_enabled', 0);
define('BANCO_PATH',__DIR__);

include 'lib/soapclient.php';
include 'lib/jbpaymentlib.php';
$endpoint = 'http://bancoeconomico.ao/economiconet/spfService';
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:spf="http://bancoeconomico.ao/economiconet/spfService">
   <soapenv:Header />
   <soapenv:Body>
      <spf:importPaymentRequest>
         <authToken>YYYYYYY</authToken>
         <entityId>12345</entityId>
         <productNumber>1</productNumber>
         <sourceId>A12345</sourceId>
         <amount>1234.56</amount>
      </spf:importPaymentRequest>
   </soapenv:Body>
</soapenv:Envelope>';
$xml = array(
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
	//$wsdl= 'http://localhost/payment_banco/lib/SPFService_v1.1.wsdl';
	$client = new RemoteSoapClient($wsdl);

	$functions = $client->__getFunctions ();
	JbPaymentbancoLib::debug ($functions);
	
	try{
		$result = $client->__soapCall($action,$xml);
		return $result;
		/*
		JbPaymentbancoLib::debug($result);
		//$result = $client->execute('',$xml,$action);
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