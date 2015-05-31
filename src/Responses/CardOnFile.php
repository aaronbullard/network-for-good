<?php namespace NetworkForGood\Responses;

class CardOnFile extends Response {

	public function getCOFId()
	{
		return $this->response->COFId;
	}

	public function getCardType()
	{
		return $this->response->CardType;
	}

	public function getCardNumberLastFour()
	{
		return $this->response->CCSuffix;
	}

	public function getExpirationMonth()
	{
		return $this->response->CCExpMonth;
	}

	public function getExpirationYear()
	{
		return $this->response->CCExpYear;
	}

	public function getEmailAddress()
	{
		return $this->response->COFEmailAddress;
	}
}