<?php namespace NetworkForGood;

use InvalidArgumentException;

trait ValidationTrait {

	protected static function validateIsInteger($number)
	{
		if( ! is_int($number))
		{
			throw new InvalidArgumentException("'$number' is not an integer.");
		}
	}

	protected static function validateMaxCharacters($string, $maxChar)
	{
		if( strlen($string) > $maxChar )
		{
			throw new InvalidArgumentException("'$string' cannot be greater than $maxChar characters.");
		}
	}
}