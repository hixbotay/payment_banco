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
			$config->merchantId = $config->sandbox_merchantId;
			$config->hashmac = $config->sandbox_hashmac;
		}
		return $config;
	}
	
	private function formatNumber($value){
		return number_format($value,2,'.','');
	}
	
	private function getPaymentUrl($testmode){
		if($testmode){
			return 'http://sandbox.banco.com/dusu_payments/banco';
		}		
		return 'https://www.banco.com/dusu_payments/banco';
	}
	
	function _prePayment($data) {
		
		$this->autoload();
		
		$host = JUri::root();
		$pingback_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=display_message&method=' . $this->_element.'&order_number='.$data['order_number']);		
		$notify_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=process&method=' . $this->_element);
		$notify_url .= '&lang='.JFactory::getLanguage()->getTag();
		$cancel_url = JString::ltrim($host.'index.php?option=com_bookpro&controller=payment&task=postpayment&paction=cancel&method=' . $this->_element.'&order_id='.$data['id']);
		$config = $this->getConfig();
		
		$params = array(
			'merchantId'	=> $config->merchantId,
			'amount' 		=> $this->formatNumber($data['total']),
			'currency'		=> $config->currency,
			'itemId'		=> $data['order_number'],
			'returnUrl'		=> $pingback_url,
			'successURL'	=> $notify_url,
			'environment'	=> $config->testmode			
		);
		$stringData = $params['banco_merchantId'].$params['banco_amount'].$params['banco_currency'].$params['banco_itemId'].$params['banco_itemName'].$params['banco_transactionReference'];
		$params['banco_hash'] =  hash_hmac('sha1', $stringData,$config->hashmac);
		
		JbPaymentbancoLib::write_log('banco.txt', 'Checkout: '.json_encode($params));
		JbPaymentbancoLib::submitForm($params,$this->getPaymentUrl($config->test_mode));
	}
	
	/**
	 *
	 *        
	 */
	function _postPayment($data) {
		
		
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
		$status = $input->getString('banco_transactionStatus');
		
		$success_status = array('CO','PA');
		
		if(in_array($status, $success_status)){
			$order_number = $input->getString('_itemId');
			$order_jb = JbPaymentbancoLib::getOrder($order_number);
			$order_jb->pay_status = 'SUCCESS';
			$order_jb->order_status = 'CONFIRMED';
			$order_jb->tx_id = $tnxref;
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
	
}
