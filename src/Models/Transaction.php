<?php namespace NetworkForGood\Models;


class Transaction extends Model {

	protected static $properties = [
		"PartnerID",
		"PartnerPW",
		"PartnerSource",
		"PartnerCampaign",
		"DonationLineItems",
		"TotalAmount",
		"TipAmount",
		"PartnerTransactionIdentifier",
		"DonorIpAddress",
		"DonorToken",
		"DonorFirstName",
		"DonorLastName",
		"DonorEmail",
		"DonorPhone",
		"DonorAddress1",
		"DonorAddress2",
		"DonorCity",
		"DonorState",
		"DonorZip",
		"DonorCountry",
		"NameOnCard",
		"CardType",
		"CardNumber",
		"ExpMonth",
		"ExpYear",
		"CSC"
	];

	protected static $optional = ['DonorAddress2'];

	protected static $propertyTypes = [
		'DonationLineItems' => 'array',
		'TotalAmount' => 'integer',
		'TipAmount' => 'integer',
		'ExpMonth' => 'integer',
		'ExpYear' => 'integer'
	];


	public function __construct(array $attributes = [])
	{
		$this->setTipAmount(0);
		parent::__construct($attributes);
	}


	public static function create($PartnerTransactionIdentifier, Partner $partner, Donor $donor, CreditCard $creditCard)
	{
		$self = new static;
		$self->setPartnerTransactionIdentifier($PartnerTransactionIdentifier);
		$self->setPartner ($partner );
		$self->setDonor( $donor );
		$self->setCreditCard( $creditCard );

		return $self;
	}

	public function setCardType($cardType)
	{
		CreditCard::validateCardType($cardType);

		$this->attributes['CardType'] = $cardType;

		return $this;
	}

	public function setPartner(Partner $partner)
	{
		$this->merge( $partner );

		return $this;
	}

	public function setDonor(Donor $donor)
	{
		$this->merge( $donor );

		return $this;
	}

	public function setCreditCard(CreditCard $creditCard)
	{
		$this->merge( $creditCard );

		return $this;
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

		foreach($this->attributes['DonationLineItems'] as $donationItem)
		{
			$total += $donationItem->getItemAmount();
		}

		$this->setTotalAmount( $total );
	}
}