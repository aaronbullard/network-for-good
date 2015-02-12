<?php namespace NetworkForGood;

use InvalidArgumentException;

class DonationLineItem implements Arrayable {

	use ValidationTrait;

	const DONORVIS_ALL = 'ProvideAll';
	const DONORVIS_ONLY_NAME_AND_EMAIL = 'ProvideNameAndEmailOnly';
	const DONORVIS_ANONYMOUS = 'Anonymous';

	const RECUR_NEVER = 'NotRecurring';
	const RECUR_MONTHLY = 'Monthly';
	const RECUR_QUARTERLY = 'Quarterly';
	const RECUR_ANNUALLY = 'Annually';

	protected $ein;

	protected $donor_vis;

	protected $dollar_amount;

	protected $recur_type;

	protected $add_or_deduct;

	protected $options;

	public function __construct($ein, $donor_vis, $dollar_amount, $recur_type, $add_or_deduct, array $options = [])
	{
		static::validateIsInteger( $dollar_amount );
		static::validateRecurType( $recur_type );
		static::validateDonorVis( $donor_vis );
		static::validateAddOrDeduct( $add_or_deduct );

		$this->ein 			= (string) $ein;
		$this->donor_vis 	= (string) $donor_vis;
		$this->dollar_amount = (int) $dollar_amount;
		$this->recur_type 	= $recur_type;
		$this->add_or_deduct= $add_or_deduct;
		$this->options 		= $options;
	}

	public function getNpoEin()
	{
		return $this->ein;
	}

	public function getDonorVis()
	{
		return $this->donor_vis;
	}

	public function getDollarAmount()
	{
		return $this->dollar_amount;
	}

	public function getRecurType()
	{
		return $this->recur_type;
	}

	public function getAddOrDeduct()
	{
		return $this->add_or_deduct;
	}

	public function toArray()
	{
		return [
			'NpoEin'		=> $this->getNpoEin(),
			'Designation'	=> $this->getDesignation(),
			'Dedication'	=> $this->getDedication(),
			'donorVis'		=> $this->getDonorVis(),
			'ItemAmount'	=> $this->getDollarAmount(),
			'RecurType'		=> $this->getRecurType(),
			'AddOrDeduct'	=> $this->getAddOrDeduct(),
			'TransactionType'=> $this->getTransactionType()
		];
	}

	public function __call($method, $args)
	{
		$option = lcfirst( substr($method, 3) );

		return isset($this->options[$option]) ? $this->options[$option] : NULL;
	}

	protected static function validateRecurType($recur_type)
	{
		$types = [
			static::RECUR_NEVER,
			static::RECUR_MONTHLY,
			static::RECUR_QUARTERLY,
			static::RECUR_ANNUALLY
		];

		if( ! in_array($recur_type, $types) )
		{
			throw new InvalidArgumentException("'$recur_type' is not a valid parameter.");
		}
	}

	protected static function validateDonorVis($donor_vis)
	{
		$types = [
			static::DONORVIS_ALL,
			static::DONORVIS_ONLY_NAME_AND_EMAIL,
			static::DONORVIS_ANONYMOUS,
		];

		if( ! in_array($donor_vis, $types) )
		{
			throw new InvalidArgumentException("'$donor_vis' is not a valid parameter.");
		}
	}

	protected function validateAddOrDeduct($add_or_deduct)
	{
		$types = ['Add', 'Deduct'];

		if( ! in_array($add_or_deduct, $types) )
		{
			throw new InvalidArgumentException("'$add_or_deduct' is not a valid parameter.");
		}		
	}
}