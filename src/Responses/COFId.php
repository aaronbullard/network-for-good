<?php namespace NetworkForGood\Responses;

class COFId extends Response {

	public function getStatusCode()
	{
		if(property_exists($this->response, 'status')){
			return $this->response->status;
		}

		return $this->response->StatusCode;
	}

	public function getCOFId()
	{
		if(property_exists($this->response, 'cardOnFileId')){
			return $this->response->cardOnFileId;
		}

		return $this->response->CofId;
	}

	public function getDonorToken()
	{
		if(property_exists($this->response, 'donorToken')){
			return $this->response->donorToken;
		}

		return $this->response->DonorToken;
	}
}
