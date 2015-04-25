<?php namespace NetworkForGood\CreditCards;

class DefaultCreditCard extends CreditCard {

	protected static $card_type = 'Unknown';

	protected $cardType;

	public function __construct($card_type, $name_on_card, $number, $exp_month, $exp_year, $csc, $cof_id = NULL)
	{
		parent::__construct($name_on_card, $number, $exp_month, $exp_year, $csc, $cof_id);

		$this->cardType = $card_type;
	}

	public function getCardType()
	{
		return $this->cardType;
	}
}