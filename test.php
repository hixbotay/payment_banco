<?php 
ini_set('soap.wsdl_cache_enabled', 0);
define('BANCO_PATH',__DIR__);

$wsdl = BANCO_PATH.'/lib/SPFService_v1.1.wsdl';
$wsdl= 'http://localhost/payment_banco/lib/SPFService_v1.1.wsdl';
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
$xml = (object)array(
'authToken' => 'YYYYYYY',
'entityId' => '12345',
'productNumber' => '1',
'sourceId' => 'A12345',
'amount' => '1234.56',
);
$client = new SoapClient($wsdl);

$functions = $client->__getFunctions ();
$result = $client->__soapCall('importPayment',$xml);
JbPaymentbancoLib::debug ($functions);
JbPaymentbancoLib::debug($client);
JbPaymentbancoLib::debug($result);
