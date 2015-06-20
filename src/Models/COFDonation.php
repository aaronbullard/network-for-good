<?php namespace NetworkForGood\Models;

class COFDonation extends Model {

	protected static $properties = [
		"DonationLineItems",
		"TotalAmount",
		"TipAmount",
		"PartnerTransactionIdentifier",
		"DonorIpAddress",
		"DonorToken",
		"COFId"
	];

	protected static $propertyTypes = [
		"DonationLineItems" => 'array',
		'TotalAmount' => 'double',
		'TipAmount' => 'double',
		'COFId' => 'integer'
	];

	public static function create($PartnerTransactionIdentifier, Donor $donor, $cofId, array $donationLineItems = [], $tipAmount = 0)
	{
		$self = new static([
			'PartnerTransactionIdentifier' => $PartnerTransactionIdentifier,
			'DonationLineItems' => $donationLineItems,
			'DonorIpAddress' => $donor->getDonorIpAddress(),
			'DonorToken' => $donor->getDonorToken(),
			'COFId' => $cofId,
			'TipAmount' => $tipAmount
		]);

		if( ! isset($self->attributes['DonationLineItems']))
			$self->attributes['DonationLineItems'] = [];

		$self->updateTotalAmount();

		return $self;
	}


	public function setDonationLineItems(array $donationLineItems)
	{
		foreach($donationLineItems as $donationItem)
		{
			$this->addDonationItem( $donationItem );
		}
	}


	public function addDonationItem(DonationItem $donationItem)
	{
		$this->attributes['DonationLineItems'][] = $donationItem;

		$this->updateTotalAmount();

		return $this;
	}


	public function countDonationItems()
	{
		return count( $this->getDonationLineItems() );
	}


	public function setTotalAmount($amount)
	{
		static::validateIsFloat($amount);

		$this->attributes['TotalAmount'] = (float) $amount;

		return $this;
	}


	protected function updateTotalAmount()
	{
		$total = 0;

		foreach($this->getDonationLineItems() as $donationItem)
		{
			$total += $donationItem->getItemAmount();
		}

		$this->setTotalAmount( $total );
	}


	public function toArray()
	{
		$array = parent::toArray();

		foreach($array['DonationLineItems'] as &$donationLineItem)
		{
			$donationLineItem = $donationLineItem->toArray();
		}

		return $array;
	}
}