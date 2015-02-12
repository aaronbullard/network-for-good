<?php namespace NetworkForGood\CreditCards;

use InvalidArgumentException;
use NetworkForGood\Arrayable;

abstract class CreditCard implements Arrayable {

	protected $cof_id;

	protected static $card_type;

	protected $name_on_card;

	protected $number;

	protected $exp_month;

	protected $exp_year;

	protected $csc;

	public function __construct($name_on_card, $number, $exp_month, $exp_year, $csc, $cof_id = NULL)
	{
		static::validateNumber( $number );
		static::validateExpirationMonth( $exp_month );
		static::validateExpirationYear( $exp_year );
		static::validateCSC( $csc );

		$this->name_on_card = (string) $name_on_card;
		$this->number 		= (string) $number;
		$this->exp_month 	= (int) $exp_month;
		$this->exp_year 	= (int) $exp_year;
		$this->csc 			= $csc;
	}

	public function getCardType()
	{
		return static::$card_type;
	}

	public function getNameOnCard()
	{
		return $this->name_on_card;
	}

	public function getCardNumber()
	{
		return $this->number;
	}

	public function getCardNumberLastFour()
	{
		return substr($this->number, -4);
	}

	public function getExpirationMonth()
	{
		return $this->exp_month;
	}

	public function getExpirationYear()
	{
		return $this->exp_year;
	}

	public function getExpiration()
	{
		return (string) $this->exp_month . '/' . $this->exp_year;
	}

	public function getCSC()
	{
		return $this->csc;
	}

	public function toArray()
	{
		if( isset($this->cof_id))
		{
			return [
				'COFId'	=> $this->cof_id
			];
		}

		return [
			'NameOnCard'	=> $this->getNameOnCard(),
			'CardType'		=> $this->getCardType(),
			'CardNumber'	=> $this->getCardNumber(),
			'ExpMonth'		=> $this->getExpirationMonth(),
			'ExpYear'		=> $this->getExpirationYear(),
			'CSC'			=> $this->getCSC()
		];
	}

	protected static function validateNumber($number)
	{
		static::validateIsString( $number );

		if( ! static::isValidCreditCardNumber($number))
		{
			throw new InvalidArgumentException("$number is not a valid credit card number.");
		}
	}

	protected static function validateExpirationMonth($exp_month)
	{
		static::validateIsPositiveInteger( $exp_month );

		if( $exp_month > 12 )
		{
			throw new InvalidArgumentException("$exp_month is not a valid month.");
		}
	}

	protected static function validateExpirationYear($exp_year)
	{
		static::validateIsPositiveInteger( $exp_year );

		if( $exp_year < (int) date('Y') )
		{
			throw new InvalidArgumentException("$exp_year is not a valid year.");
		}
	}

	protected static function validateIsPositiveInteger($value)
	{
		if( ! is_int($value) || $value < 1)
		{
			throw new InvalidArgumentException("$value is not a positive integer.");
		}
	}

	protected static function validateIsString($string)
	{
		if( ! is_string($string) )
		{
			throw new InvalidArgumentException("$string is not a string.");
		}
	}

	protected static function isValidCreditCardNumber($cc_number)
	{
		return static::typeOfCard($cc_number) ? TRUE : FALSE;
	}

	protected static function typeOfCard($cc_number) {
	   /* Validate; return value is card type if valid. */
	   $false = false;
	   $card_type = "";
	   $card_regexes = array(
		  "/^4\d{12}(\d\d\d){0,1}$/" => "visa",
		  "/^5[12345]\d{14}$/"       => "mastercard",
		  "/^3[47]\d{13}$/"          => "amex",
		  "/^6011\d{12}$/"           => "discover",
		  "/^30[012345]\d{11}$/"     => "diners",
		  "/^3[68]\d{12}$/"          => "diners",
	   );
	 
	   foreach ($card_regexes as $regex => $type) {
		   if (preg_match($regex, $cc_number)) {
			   $card_type = $type;
			   break;
		   }
	   }
	 
	   if (!$card_type) {
		   return $false;
	   }
	 
	   /*  mod 10 checksum algorithm  */
	   $revcode = strrev($cc_number);
	   $checksum = 0; 
	 
	   for ($i = 0; $i < strlen($revcode); $i++) {
		   $current_num = intval($revcode[$i]);  
		   if($i & 1) {  /* Odd  position */
			  $current_num *= 2;
		   }
		   /* Split digits and add. */
			   $checksum += $current_num % 10; if
		   ($current_num >  9) {
			   $checksum += 1;
		   }
	   }
	 
	   if ($checksum % 10 == 0) {
		   return $card_type;
	   } else {
		   return $false;
	   }
	}

	public static function validateCSC($csc)
	{
		static::validateIsString( $csc );

		$to_integer = (int) $csc;

		if( $to_integer == 0 || $to_integer > 9999)
		{
			throw new InvalidArgumentException("$csc is not a proper value.");
		}
	}
}