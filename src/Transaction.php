<?php namespace NetworkForGood;

use Exception;
use NetworkForGood\CreditCards\CreditCard;

class Transaction implements Arrayable {

	use ValidationTrait;

	protected $params = [];

	protected $donations = [];

	protected $use_objects = TRUE;

	private function __construct(array $params)
	{
		$this->params = $params;

		if( ! isset($params['Donor']))
		{
			$this->use_objects = FALSE;
		}
	}

	public static function create(Partner $partner, Donor $donor, CreditCard $cc, $tip_amount = NULL, $trans_id = NULL)
	{
		if( isset( $tip_amount))
		{
			static::validateIsInteger( $tip_amount );
		}

		if( isset($trans_id))
		{
			static::validateIsInteger( $trans_id );
		}

		$params = [
			'Partner'	=> $partner,
			'Donor'		=> $donor,
			'CreditCard'=> $cc,
			'TipAmount'	=> $tip_amount,
			'PartnerTransactionIdentifier' => $trans_id
		];

		return new static($params);
	}

	public static function createByIds(Partner $partner, $donorToken, $card_on_file_id, $tip_amount = 0, $trans_id = NULL)
	{
		if( isset( $tip_amount))
		{
			static::validateIsInteger( $tip_amount );
		}

		if( isset($trans_id))
		{
			static::validateIsInteger( $trans_id );
		}

		$params = [
			'Partner'	=> $partner,
			'DonorToken'=> $donorToken,
			'COFId'		=> $card_on_file_id,
			'TipAmount'	=> $tip_amount,
			'PartnerTransactionIdentifier' => $trans_id
		];

		return new static($params);
	}

	public function __call($method, $args)
	{
		$param = substr($method, 3);

		if( isset($this->params[$param]) )
		{
			return $this->params[$param];
		}

		throw new Exception("Method not found");
	}

	public function addDonationLineItem(DonationLineItem $donation)
	{
		$this->donations[] = $donation;

		return $this;
	}

	public function getDonationLineItems()
	{
		return $this->donations;
	}

	public function countDonationLineItems()
	{
		return count( $this->donations );
	}

	public function getPartner()
	{
		return $this->params['Partner'];
	}

	public function getDonor()
	{
		return $this->params['Donor'];
	}

	public function getCreditCard()
	{
		return $this->params['CreditCard'];
	}

	public function getTotalAmount()
	{
		$total = 0;

		foreach( $this->donations as $donation)
		{
			$total += $donation->getDollarAmount();
		}

		return $total;
	}

	public function getTipAmount()
	{
		return $this->params['TipAmount'];
	}

	public function getPartnerTransactionId()
	{
		return $this->params['PartnerTransactionIdentifier'];
	}

	public function toArray()
	{
		return $this->use_objects ? $this->toArrayWithObjects() : $this->toArrayWithIds();
	}

	protected function makeTransactionArray()
	{
		$donationLineItems = [];
		foreach($this->getDonationLineItems() as $donation)
		{
			$donationLineItems[] = $donation->toArray();
		}

		$donorTransaction = [
			'DonoationLineItems' => $donationLineItems,
			'TotalAmount'	=> $this->getTotalAmount(),
			'TipAmount' => $this->getTipAmount()
		];

		if( ! is_null($this->getPartnerTransactionId()))
		{
			$donorTransaction['PartnerTransactionIdentifier'] = $this->getPartnerTransactionId();
		}

		return $donorTransaction;
	}

	protected function toArrayWithObjects()
	{
		$partner 		= $this->getPartner()->toArray();
		$transaction 	= $this->makeTransactionArray();
		$donor 			= $this->getDonor()->toArray();
		$creditCard 	= $this->getCreditCard()->toArray();

		return array_merge($partner, $transaction, $donor, $creditCard);
	}

	protected function toArrayWithIds()
	{
		$partner = $this->getPartner()->toArray();

		$transaction = $this->makeTransactionArray();
		$transaction['DonorToken'] 	= $this->getDonorToken();
		$transaction['COFId']		= $this->getCOFId();

		return array_merge($partner, $transaction);
	}
}