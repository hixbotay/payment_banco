<?php 
define('_JEXEC',1);
require 'jbdefines.php';
require 'lib/jbpaymentlib.php';
require 'lib/soapclient.php';
require JPATH_ROOT.'/components/com_bookpro/controllers/payment.php';
/*
$res = file_get_contents('php://input');
if(!$res){
	$res = stream_get_contents(STDIN);
}
*/

JbPaymentbancoLib::write_log('banco.txt', 'IPN: '.json_encode($_REQUEST));

$app =JFactory::getApplication('site');
$app->input->set('method','payment_banco');
$app->input->set('paction','process');

$controller = new BookProControllerPayment();
$controller->postpayment();

echo 'DONE';
exit;