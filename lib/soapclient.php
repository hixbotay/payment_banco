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
		
		return $this->__doRequest($request, $location, $action, '1');
		/*
		$soapEnvelope = new SimpleXMLElement($result);
		$name_spaces = $soapEnvelope->getNamespaces(true);
		$namespace = isset($name_spaces['soap-env']) ? $name_spaces['soap-env'] : $name_spaces['SOAP-ENV'];
		if(isset($soapEnvelope->children($namespace)->Body->Fault)){
			JbPaymentbancoLib::write_log('banco.txt', $call.' error '.json_encode($soapEnvelope->children($name_spaces['soap-env'])->Body->Fault->children()));
			$error = (string)$soapEnvelope->children($name_spaces['soap-env'])->Body->Fault->children()->faultstring;
			
			$soapEnvelope = null;
			if($config->debug){
				debug($result,true);
			}else{
				throw new Exception($call.' '. $error,'405');
			}
				
		}
		$soapEnvelope = null;
		unset($soapEnvelope);
		return $result;
		*/
	}
}