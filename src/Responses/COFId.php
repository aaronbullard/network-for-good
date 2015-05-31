<?php namespace NetworkForGood\Responses;

class COFId extends Response {

	public function getCOFId()
	{
		return $this->response->CofId;
	}

	public function getDonorToken()
	{
		return $this->response->DonorToken;
	}
}