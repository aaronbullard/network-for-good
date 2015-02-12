<?php namespace NetworkForGood;

use Faker\Factory as Faker;
use InvalidArgumentException;

class Test extends CreditCard {

	protected static $cards = [
		'Amex' 			=> [371449635398431, 378282246310005],
		'Mastercard'	=> [5105105105105100, 5555555555554444],
		'Visa'			=> [4012888888881881, 4111111111111111]
	];

	protected static $card_type = 'Test';

	public static function create($type, $version = 0)
	{
		static::validateType( $type );

		$faker = Faker::create();

		$csc = (string) $faker->numberBetween(1000, 9999);

		return new static($faker->name, static::$cards[$type][0], $faker->numberBetween(0, 12), date('Y'), $csc);
	}

	protected static function validateType( $type )
	{
		if( ! isset( static::$cards[$type] ) )
		{
			throw new InvalidArgumentException("$type card type is not recognized.");
		}
	}
}