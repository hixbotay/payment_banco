<?php 
ini_set('soap.wsdl_cache_enabled', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BANCO_PATH',__DIR__);

// include 'lib/soapclient.php';
include 'lib/jbpaymentlib.php';

$wsdl='https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
$name = BANCO_PATH.'/wsdl/WSI_PaymentRefCreate.wsdl';
$content= JbPaymentbancoLib::get_data_from_url($wsdl);
echo 'RESPONSE: from'.$wsdl.PHP_EOL;
JbPaymentbancoLib::debug($content);
$fh = fopen($name, 'w');
fwrite($fh, $content);
fclose($fh);

$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?XSD=../Schemas/PaymentRefCreate.xsd';
$content= JbPaymentbancoLib::get_data_from_url($wsdl);
echo 'RESPONSE: from'.$wsdl.PHP_EOL;

$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate';
$content= JbPaymentbancoLib::get_data_from_url($wsdl);
echo 'RESPONSE: from'.$wsdl.PHP_EOL;

if(!$content){
    echo 'Not response from '.$wsdl;
    //return;
}

$wsdl=  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery?WSDL
';
$wsdl = 'https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery?WSDL';
$name = BANCO_PATH.'/wsdl/WSI_PaymentRefDetailsQuery.wsdl';
$content= JbPaymentbancoLib::get_data_from_url($wsdl);
$fh = fopen($name, 'w');
fwrite($fh, $content);
fclose($fh);

echo 'RESPONSE: from'.$wsdl.PHP_EOL;
JbPaymentbancoLib::debug($content);
if(!$content){
    echo 'Not response from '.$wsdl;
    //return;
}
echo 'done';
