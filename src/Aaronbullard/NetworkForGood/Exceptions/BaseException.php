<?php namespace NetworkForGood\Exceptions;

use Exception;

class BaseException extends Exception {

	protected $errorDetails;

	protected $response;

	public function setResponse($response)
	{
		$this->response = $response;
		return $this;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function setErrorDetails($errorDetails)
	{
		$this->errorDetails = $errorDetails;
		return $this;
	}

	public function getErrorDetails()
	{
		return $this->errorDetails;
	}
};