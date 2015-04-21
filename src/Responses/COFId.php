<?php namespace NetworkForGood\Responses;

class COFId {

	protected $cof;

	public function __construct($cof)
	{
		$this->cof = $cof;
	}

	public function getCOFId()
	{
		return $this->cof->CofId;
	}

	public function getDonorToken()
	{
		return $this->cof->DonorToken;
	}
}