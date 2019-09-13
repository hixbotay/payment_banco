<?php
/**
 * @package Bookpro
 * @author Ngo Van Quan
 * @link http://joombooking.com
 * @copyright Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version $Id: request.php 44 2012-07-12 08:05:38Z quannv $
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
require_once (JPATH_ADMINISTRATOR . '/components/com_bookpro/helpers/payment.php');
define('BANCO_PATH',JPATH_ROOT.'/plugins/bookpro/payment_banco');


// require_once (JPATH_ADMINISTRATOR . '/components/com_bookpro/helpers/log.php');
class plgBookproPayment_banco extends BookproPaymentPlugin {
	var $_element = 'payment_banco';
	
	function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );	
		$language = JFactory::getLanguage ();	
		$language->load ( 'plg_bookpro_payment_banco', JPATH_ADMINISTRATOR );
	}
	
	private function getConfig(){
		$config = $this->params->toObject();
    	$currency = JComponentHelper::getParams('com_bookpro')->get('main_currency');
		$config->currency = $currency;
		if($config->test_mode){
			$config->token = 'MTU2NzE3OTg1MDk4Njo0ZjFiNjA0MzE4ODYxODc3ODgzYjVkYmUyNzAzYWY0NGM5ZGRlOWI3NzBkNTc2MzdmODI0NzM0NmYwNzQzNmYxYTczYTY2MzlmNTQ4Mjk3MTY4NTBjOTMxNmU2MDg3YjAzNWJiNWVhMTk0NDZiZmYxMGJjZGE5ODhjMzhmNWEyODc2ZGM4MjE0ZmFiOGM0NWViZDFlZjg4OWM2MmI1MDY1YzVhZjMwYzM2NjczM2U4Y2VmY2IyZTUxOTNjMWIyMjA4YTc0M2Y1ZTRhMzk4ZGU4NTc2YmU4ODBmMjZkM2I2MTZjNmNhZjJhYTNhNTY5NDIwYWNlMGZlOTUwZTc1Mjc3NzE1NTBkYjNiYjI5ODMzM2E5NzRmMzNkNGZmNTgwMDc4N2I5NDM1YmU0ZjFlZTMzNWJjMDIzZjc4YzlmZDA2NDliN2NjNjIwYWNlMmI3YzZkMmU1ZTM4ZWNhNmVkZmVhODI5NTM2ODBiYWFmMDAxNmRlNTY1NTRjNjEyMTJmZDNiODE0Nzk0YTZlZjk5N2RlNjAxMDI0YTEzYjJjNWM3OTFiZjlkNTU0ODZkNTdhYTk5OTEwNzc0MmY4ZmUyZDg0YmQ1ZjA1ZWNlMjAxMmIyYjFhYmNhMGIwNGFjYTNjZDY0YjA5MWZlYjdlMDc3ZGMzYTkzMjg4ODVmZTZlOWRmODowMDU4OF8xOmViNjYwNjIwMTU2Yjg0ZjdiMTAzZWQzMWM3MWYzMzQ3NGM5MTBiOThjODRlOWNjNmJlMWZjMGZkMDBiY2E0Nzg3ZTlmNDc5MGU3NjU4MmVhM2MxNjFmMWFhNzgxMGNkOTI2MTVhMjhkNjZhMTQwN2FlZWMyN2VjMGVhNzdjMGUx';
			$config->user_id='FLY001';
			$config->username='ECN';
			$config->entity_id='00588';
			$config->source_id='FLY001';
			$config->source='BANCO';
			$config->password='password1';			
		}else{
			if(!$config->token) $config->token='MTU2NTYyMzMzMjg4NTo4ZmZlOTIzMDc5MjhjZDEwYTEzZmFiYjRjMDQ0Y2M3ZDFjNjVlNzNiNGRjYjg3YjcxNzM4ZDA3M2RlMDllNDkwZmE0YjU4NjYyOWU4OTlhYTFkN2UzM2I2MzJhNDI0OGU5MjQ3Zjc1YWM5ZTRkODE1MDQzYTY5MzZmNTM5MmNlZjI0NTA3NjQ2ZTRjM2UzZTVjY2NiOTcyNzg4ZDQwZDZlZmI5ZDE3OGFkYWMxZjE5YjVhNWMxNzY4NjA4OWQ2OGRkNjA5NmQzYzcxZjhmOTE2MjI2ZGJjYmMyYTMwNTMzNDdkMGYyM2FiZTMyMjQ4ODI3ZDdmNGIzNjdhYzQ4YzEwMTU2YmU4MTQ0MThkMjc2YzYzZDhmMzNlYmViYTdiMDhjZDg2NzcyMjY4MDE3OGM0ZGJlZDMwOWI3NTkyYTg0MTgxNDA4NWQyYjkwYzg5YTgyYzFhMzZlNGJlOTE0ZmRkZTc0NzU5NzU0N2VhMGRiYjM1NWM2OTdkYjQxOTE5NmY3MWM4MTc5M2JmODc5MTQxOTlmODljMGRhYWJlYmUyZmQ2MDdiYzhhNDExYmY5YWRhOWZlMjg1NzNmOTFmZTRlNzhjMTUzMDVhNzEzNWFjNjI4NjZiMzA1NDU4OGM5NTY1ZjVhYjBlMjQ2MDI2MTQzZWUyNWE5NDE3YzAyOTc1OTowMDU4OF8xOmFmYTVmNjI1OTk5NWM0ZjU2MDk5MDc0MmJkOTVhMGM4ZGQzZDQzM2UyZjEzODdmYmJmOWJjNmNhOTIwN2UzMWQ1MWU2ZDk4NDNlMWIwNTk5ZGVjMjhhYjEyZTdlNzVhMGEzZWQ4YmU2OWYwYTM1NjllZjRlNDZlMTVhMTE0NmMw';
		}
		return $config;
	}
	
	private function formatNumber($value){
		return number_format($value,0,'','');
	}
	
	function getCustomerInfo(){
		
	}
	
	
	function _prePayment($data) {
		
		$this->autoload();
		
		$host = JUri::root();
		$pingback_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=display_message&method=' . $this->_element.'&order_number='.$data['order_number']);		
		$notify_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=process&method=' . $this->_element);
		$notify_url .= '&lang='.JFactory::getLanguage()->getTag();
		$cancel_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=cancel&method=' . $this->_element.'&order_id='.$data['id']);
		$config = $this->getConfig();
		$total = $this->formatNumber($data['total']);
		$start_pay_date = (new DateTime())->format('Y-m-d');
		$end_pay_date = (new DateTime('+1 days'))->format('Y-m-d');
		
		if(!data['email'] || !$data['id']){
			if(!$data['id']){
				$order = JbPaymentbancoLib::getOrder($data['order_number']);
				$data['id'] = $order->id;
			}
			$customer = JbPaymentbancoLib::getCustomerByOrderID($data['id']);
			$data['email'] = $customer->email;
			$data['phone'] = $customer->mobile;
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pay="http://www.bancoeconomico.ao/xsd/paymentref">
		'.getHeader($config).'
		<soapenv:Body>
			<pay:PaymentRefCreateRequest>
			<pay:HEADER>
				<pay:SOURCE>'.$config->source.'</pay:SOURCE>
				<pay:MSGID>'.$this->gen_uuid().'</pay:MSGID>
				<pay:USERID>'.$config->user_id.'</pay:USERID>
				<pay:BRANCH>000</pay:BRANCH>
				<!--Optional:-->
				<pay:PASSWORD/>
				<pay:INVOKETIMESTAMP>'.(new DateTime())->format('Y-m-d\TH:i:s').'</pay:INVOKETIMESTAMP>
			</pay:HEADER>
			<pay:BODY>
				<pay:Payment>
					<pay:AUTHTOKEN>'.$config->token.'</pay:AUTHTOKEN>
					<pay:ENTITYID>'.$config->entity_id.'</pay:ENTITYID>
					<pay:PRODUCT_NO>1</pay:PRODUCT_NO>
					<pay:SOURCE_ID>'.$config->source_id.'</pay:SOURCE_ID>
					<!--Optional:-->
				   <pay:REFERENCE>'.$data['id'].'</pay:REFERENCE>
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
				   <pay:EMAIL>'.$data['email'].'</pay:EMAIL>
				   <!--Optional:-->
				   <pay:PHONE_NUMBER/>'.$data['phone'].'</pay:PHONE_NUMBER>
				</pay:Payment>
			</pay:BODY>
			</pay:PaymentRefCreateRequest>
		</soapenv:Body>
		</soapenv:Envelope>';
		
		if($config->test_mode){			
			$wsdl = BANCO_PATH.'/wsdl/WSI_PaymentRefCreate.wsdl';
			$wsdl_endpoint = 'https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate';			
		}else{
			$wsdl =  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
			$wsdl_endpoint = 'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefCreate/WSI_PaymentRefCreate?wsdl';
		}
		
		$action = 'WSI_PaymentRefCreate';
		$client = new RemoteSoapClient($wsdl);
		$result = $client->execute($wsdl_endpoint,$xml,$action);
		if($result->error){
			JFactory::getApplication()->enqueueMessage($result->error,'error');
		}else{
			JFactory::getApplication()->enqueueMessage($config->msg_confirm);
			$tx_id = $result->PaymentRefCreateResponse->BODY->Payment_Details->PAYMENT_ID;
			$order = JbPaymentbancoLib::getOrder($data['order_number'],$data['id']);
			$order->tx_id = $tx_id;
			$order->store();
		}	
			
		JFactory::getApplication()->redirect('index.php?ItemId=0');	
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
	/**
	 *
	 *        
	 */
	function _postPayment($data) {
		die('df');
		
		// Process the payment
		$input = JFactory::getApplication ()->input;
		
		$paction = $input->getString ( 'paction' );
		
		$vars = new JObject ();
		
		switch ($paction) {
			
			case "display_message" :
				
				return $this->displaymsg ();
				
				break;
			
			case "process" :
				
				return $this->_processSale ();
				
				$app = JFactory::getApplication ();
				
				$app->close ();
				
				break;
			
			case "cancel" :
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_BANCO_CANCEL'));
				JFactory::getApplication()->redirect('index.php?Itemid=0');
				return;		
				
				break;
			
			default :
				
				JFactory::getApplication()->enqueueMessage(JText::_('Invalid action'));
				JFactory::getApplication()->redirect('index.php?Itemid=0');
				return;		
				
				break;
		}
		
		return $html;
	}
	function displaymsg() {
		$this->autoload();
		$input = jfactory::getApplication()->input;
		$order_number = $input->getString('order_number');
		$order_jb = $this->_getOrder($order_number);
		$status = $input->getString('status');
		
		if(!$status == 'success'){
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_BANCO_TRANSACTION_FAILED'),'warning');
			
		}
		$order_jb->sendemail = 1;//no send email
		
		return $order_jb;	
				
			
		
	}
	
	/**
	 * Prepares variables for the payment form
	 *
	 * @return unknown_type
	 *
	 */
	function _renderForm($data) {
		$user = JFactory::getUser ();
		
		$vars = new JObject ();
		
		$html = $this->_getLayout ( 'form', $vars );
		
		return $html;
	}
	
	private function getIPNSCheckUrl($testmode){
		if($testmode == 'sandbox'){
			return 'https://sandbox.banco.com/transactions/check_status/';
		}		
		return 'https://banco.com/transactions/check_status/';
	}
	
	/**
	 * Processes the sale payment
	 *
	 */
	function _processSale() {
		$this->autoload();		
		JbPaymentbancoLib::write_log('banco.txt', 'IPN: '.json_encode($_REQUEST));
		
		$input = jfactory::getApplication()->input;		

		$config = $this->getConfig();
		$tx_id = '';
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
				<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pay="http://www.bancoeconomico.ao/xsd/paymentrefdetails">
				   '.getHeader($config).'
				   <soapenv:Body>
					  <pay:PaymentRefDetailsQueryRequest>
						  <pay:HEADER>
							<pay:SOURCE>'.$config->source.'</pay:SOURCE>
							<pay:MSGID>'.$this->gen_uuid().'</pay:MSGID>
							<pay:USERID>'.$config->user_id.'</pay:USERID>
							<pay:BRANCH>000</pay:BRANCH>
							<!--Optional:-->
							<pay:PASSWORD/>
							<pay:INVOKETIMESTAMP>'.(new DateTime())->format('Y-m-d\TH:i:s').'</pay:INVOKETIMESTAMP>
						 </pay:HEADER>
						 <pay:BODY>
							<pay:Payment>
							   <pay:AUTHTOKEN>'.$config->token.'</pay:AUTHTOKEN>
							   <pay:ENTITYID>'.$config->entity_id.'</pay:ENTITYID>
							   <!--Optional:-->
							   <pay:PaymentIdList>
								  <!--Zero or more repetitions:-->
								  <pay:PAYMENT_ID>'.$tx_id.'</pay:PAYMENT_ID>
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
		
		if($config->test_mode){						
			$wsdl = BANCO_PATH.'/wsdl/WSI_PaymentRefDetailsQuery.wsdl';			
			$wsdl_endpoint = 'https://spf-webservices-uat.bancoeconomico.ao:7443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery';
			$action = 'WSI_PaymentRefDetailsQuery';
		}else{
			$wsdl =  'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery?WSDL';
			$wsdl_endpoint = 'https://spf-webservices.bancoeconomico.ao:8443/soa-infra/services/SPF/WSI_PaymentRefDetailsQuery/WSI_PaymentRefDetailsQuery?WSDL';
		}
		
		$action = 'WSI_PaymentRefDetailsQuery';		
		$client = new RemoteSoapClient($wsdl);
		$result = $client->execute($wsdl_endpoint,$xml,$action);
		
		$success_status = array('SUCCESS');
		$status = $result->PaymentRefDetailsQueryResponse->BODY->Payment_List->Payment_Details->Status;
		$order_id = $result->PaymentRefDetailsQueryResponse->BODY->Payment_List->Payment_Details->REFERENCE;
		if(in_array($status, $success_status)){			
			$order_jb = JbPaymentbancoLib::getOrder(false,$order_id);
			$order_jb->pay_status = 'SUCCESS';
			$order_jb->order_status = 'CONFIRMED';
			$order_jb->tx_id = $tx_id;
			$order_jb->store ();
			return $order_jb;	
		}else{
			exit;
		}
		
		
		
	}
	
	private function autoload(){
		foreach (glob(JPATH_ROOT.'/plugins/bookpro/payment_banco/lib/*.php') as $filename)
		{
			require $filename;
		}
	}
	
	private function debug($val,$die =true){
		echo '<pre>';
		print_r($val);
		echo '</pre>';
		if($die){
			die;
		}
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
	
}
