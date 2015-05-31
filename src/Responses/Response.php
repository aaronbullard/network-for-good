<?php namespace NetworkForGood\Responses;

abstract class Response {

	protected $response;

	public function __construct($response)
	{
		$this->response = $response;
	}

	public function getStatusCode()
	{
		return $this->response->StatusCode;
	}
}