<?php namespace NetworkForGood\Responses;

class CardOnFile {

	protected $COFRecord;

	public function __construct($COFRecord)
	{
		$this->COFRecord = $COFRecord;
	}

	public function getCOFId()
	{
		return $this->COFRecord->COFId;
	}

	public function getCardType()
	{
		return $this->COFRecord->CardType;
	}

	public function getCardNumberLastFour()
	{
		return $this->COFRecord->CCSuffix;
	}

	public function getExpirationMonth()
	{
		return $this->COFRecord->CCExpMonth;
	}

	public function getExpirationYear()
	{
		return $this->COFRecord->CCExpYear;
	}

	public function getEmailAddress()
	{
		return $this->COFRecord->COFEmailAddress;
	}
}