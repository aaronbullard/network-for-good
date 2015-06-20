<?php namespace NetworkForGood\Models;


class CreditCardTest extends \Codeception\TestCase\Test
{
	/**
	* @var \UnitTester
	*/
	protected $tester;

	protected function getCreditCardInputs()
	{
		$name = 'James Aaron Bullard';
		$number = '371449635398431';
		$exp_month = 12;
		$exp_year = 2018;
		$csc = '303';

		return [
			"NameOnCard" => $name,
			"CardType" => 'Visa',
			"CardNumber" => $number,
			"ExpMonth" => $exp_month,
			"ExpYear" => $exp_year,
			"CSC" => $csc
		];
	}

	public function testAValidCreditCard()
	{		
		$cardInputs = $this->getCreditCardInputs();

		$cc = new CreditCard($cardInputs);

		$this->assertEquals('Visa', $cc->getCardType());
		$this->assertEquals($cardInputs['NameOnCard'], $cc->getNameOnCard());
		$this->assertEquals($cardInputs['CardNumber'], $cc->getCardNumber());
		$this->assertEquals('8431', $cc->getCardNumberLastFour());
		$this->assertEquals($cardInputs['ExpMonth'], $cc->getExpirationMonth());
		$this->assertEquals($cardInputs['ExpYear'], $cc->getExpirationYear());
		$this->assertEquals('12/2018', $cc->getExpiration());
		$this->assertEquals($cardInputs['CSC'], $cc->getCSC());
	}

	public function testAnInvalidNumber()
	{
		$this->setExpectedException('InvalidArgumentException', "123 is not a valid credit card number.");
		
		$cardInputs = $this->getCreditCardInputs();

		$cardInputs['CardNumber'] = '123';

		$cc = new CreditCard($cardInputs);
	}

	public function testAnInvalidExpirationMonth()
	{
		$this->setExpectedException('InvalidArgumentException', "13 is not a valid month.");
		
		$cardInputs = $this->getCreditCardInputs();

		$cardInputs['ExpMonth'] = 13;

		$cc = new CreditCard($cardInputs);
	}

	public function testAnInvalidExpirationYear()
	{
		$this->setExpectedException('InvalidArgumentException', "ExpYear is of type string not of type integer.");
		
		$cardInputs = $this->getCreditCardInputs();

		$cardInputs['ExpYear'] = '2018';

		$cc = new CreditCard($cardInputs);
	}

	public function testAnInvalidExpirationYearAgain()
	{
		$this->setExpectedException('InvalidArgumentException', "2013 is not a valid year.");
		
		$cardInputs = $this->getCreditCardInputs();

		$cardInputs['ExpYear'] = 2013;

		$cc = new CreditCard($cardInputs);
	}

	public function testAnInvalidCSC()
	{
		$this->setExpectedException('InvalidArgumentException', "CSC is of type integer not of type string.");
		
		$cardInputs = $this->getCreditCardInputs();

		$cardInputs['CSC'] = 1111;

		$cc = new CreditCard($cardInputs);
	}

	public function testTheDefaultCardType()
	{		
		$cardInputs = $this->getCreditCardInputs();

		$cc = new CreditCard($cardInputs);

		$this->assertEquals('Visa', $cc->getCardType());
		$this->assertEquals($cardInputs['NameOnCard'], $cc->getNameOnCard());
		$this->assertEquals($cardInputs['CardNumber'], $cc->getCardNumber());
		$this->assertEquals('8431', $cc->getCardNumberLastFour());
		$this->assertEquals($cardInputs['ExpMonth'], $cc->getExpirationMonth());
		$this->assertEquals($cardInputs['ExpYear'], $cc->getExpirationYear());
		$this->assertEquals('12/2018', $cc->getExpiration());
		$this->assertEquals($cardInputs['CSC'], $cc->getCSC());
	}

}