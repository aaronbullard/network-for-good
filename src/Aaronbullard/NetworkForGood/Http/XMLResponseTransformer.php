<?php namespace NetworkForGood\Http;

class XMLResponseTransformer {

	protected $xml;

	protected $json;

	public function __construct($xml)
	{
		$this->xml = $xml;
		$this->json = json_encode( simplexml_load_string($this->xml) );
	}

	public function toObject()
	{
		return json_decode($this->json);
	}

	public function toArray()
	{
		return json_decode($this->json, TRUE);
	}
}