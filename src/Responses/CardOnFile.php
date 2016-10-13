<?php namespace NetworkForGood\Responses;

class CardOnFile extends Response {

	public function getStatusCode()
	{
		if(property_exists($this->response, 'status')){
			return $this->response->status;
		}

		return $this->response->StatusCode;
	}

	public function getCOFId()
	{
		if(property_exists($this->response, 'id')){
			return $this->response->id;
		}

		return $this->response->COFId;
	}

	public function getCardType()
	{
		if(property_exists($this->response, 'type')){
			return $this->response->type;
		}

		return $this->response->CardType;
	}

	public function getCardNumberLastFour()
	{
		if(property_exists($this->response, 'suffix')){
			return $this->response->suffix;
		}

		return $this->response->CCSuffix;
	}

	public function getExpirationMonth()
	{
		if(property_exists($this->response, 'expiration')){
			return $this->response->expiration->month;
		}

		return $this->response->CCExpMonth;
	}

	public function getExpirationYear()
	{
		if(property_exists($this->response, 'expiration')){
			return $this->response->expiration->year;
		}

		return $this->response->CCExpYear;
	}

	public function getEmailAddress()
	{
		if(property_exists($this->response, 'email')){
			return $this->response->email;
		}

		return $this->response->COFEmailAddress;
	}
}
