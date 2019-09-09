<?php

class RemoteSoapClient extends SoapClient {
	function __construct($wsdl,$options=array()) {
		return parent::__construct($wsdl, $options);
		//$this->server = new SoapServer($wsdl, $options);
	}
	public function __doRequest($request, $location, $action, $version,$one_way = NULL) {
		$result = parent::__doRequest($request, $location, $action, $version, $one_way);
		return $result;
	}
	function execute($location, $request,$action='') {
		$res = $this->__doRequest($request, $location, $action, '1');
		JbPaymentbancoLib::write_log('banco.txt',"-----Request {$action} {$location}-----".PHP_EOL."{$request}".PHP_EOL."-----RESPONSE----- ".PHP_EOL.$res);
		$result = [];
		$soapEnvelope = new SimpleXMLElement($res);
		$name_spaces = $soapEnvelope->getNamespaces(true);		
		$namespace = isset($name_spaces['env']) ? $name_spaces['env'] : $name_spaces['ENV'];
		$result = $soapEnvelope->children($namespace)->Body;
		if(isset($soapEnvelope->children($namespace)->Body->Fault)){
			$error = (string)$soapEnvelope->children($namespace)->Body->Fault->children()->faultstring;		
			JbPaymentbancoLib::write_log('banco.txt', '-----ERROR-----'.PHP_EOL.$error);
			$result->error = $soapEnvelope->children($namespace)->Body->Fault->children();
		}
		
		$soapEnvelope = null;
		unset($soapEnvelope);
		return $result;
		
	}
} 