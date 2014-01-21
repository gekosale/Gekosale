<?php 

namespace SOAP;

/*
version: 0.3
*/

abstract class Connector {

	private $client;
	private $wsdlLocation;
	
	protected function setWsdl($wsdl = '')
	{
		$this->wsdlLocation = $wsdl;
	}

	protected function getClient()
	{
		$this->client = new \Zend\Soap\Client($this->wsdlLocation);
		$this->client ->setEncoding("UTF-8"); 
		$this->client ->setSoapVersion(SOAP_1_2);
		return $this->client;
	}

	protected function connect($method = '', $data)
	{
		$this->client = new \Zend\Soap\Client($this->wsdlLocation);
		$this->client ->setEncoding("UTF-8"); 
		$this->client ->setSoapVersion(SOAP_1_2);

		try 
		{
			$response = $this->client->$method($data);
		} 
		catch (\SoapFault $e) 
		{
			throw new \SOAP\Connector\Exception('SOAP failed with a message: '.$e->getMessage());
		}
		
		return $response;
	}
	
	protected function showLastRequest()
	{
		return $this->client->getLastRequest();
	}
	
	protected function showLastResponse()
	{
		return $this->client->getLastResponse();
	}
	
	protected function getLastRequestHeaders()
	{
		return $this->client->getLastRequestHeaders();
	}
	
	protected function getLastResponseHeaders()
	{
		return $this->client->getLastResponseHeaders();
	}
}