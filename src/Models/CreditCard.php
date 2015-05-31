<?php namespace NetworkForGood\Models;

use InvalidArgumentException;

class CreditCard extends Model {

	protected static $properties = [
		"NameOnCard",
		"CardType",
		"CardNumber",
		"ExpMonth",
		"ExpYear",
		"CSC"
	];

	protected static $propertyTypes = [
		'ExpMonth' => 'integer',
		'ExpYear' => 'integer'
	];

	protected static $cardTypes = ['Unk', 'Visa', 'Mastercard', 'Amex', 'Discover'];

	public static function getValidCardTypes()
	{
		return static::$cardTypes;
	}

	public static function validateCardType($cardType)
	{
		if( ! in_array($cardType, static::getValidCardTypes()))
			throw new InvalidArgumentException("$cardType is an invalid card type.");
	}
}