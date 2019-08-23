<?php 
ini_set('soap.wsdl_cache_enabled', 0);
define('BANCO_PATH',__DIR__);

include 'lib/soapclient.php';
include 'lib/jbpaymentlib.php';

$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
$name = BANCO_PATH.'/wsdl/WSI_PaymentRefCreate.wsdl';
$content= file_get_contents($wsdl);
$fh = fopen($name, 'w');
fwrite($fh, $content);
fclose($fh);

$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery?WSDL
';
$name = BANCO_PATH.'/wsdl/WSI_PaymentRefDetailsQuery.wsdl';
$content= file_get_contents($wsdl);
$fh = fopen($name, 'w');
fwrite($fh, $content);
fclose($fh);