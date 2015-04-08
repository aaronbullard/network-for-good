<?php namespace NetworkForGood\CreditCards;


class CreditCardTest extends \Codeception\TestCase\Test {
	
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

		return compact('name', 'number', 'exp_month', 'exp_year', 'csc');
	}

	public function testAValidCreditCard()
	{
		extract( $this->getCreditCardInputs() );
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);

		$this->assertEquals('Visa', $cc->getCardType());
		$this->assertEquals($name, $cc->getNameOnCard());
		$this->assertEquals($number, $cc->getCardNumber());
		$this->assertEquals('8431', $cc->getCardNumberLastFour());
		$this->assertEquals($exp_month, $cc->getExpirationMonth());
		$this->assertEquals($exp_year, $cc->getExpirationYear());
		$this->assertEquals('12/2018', $cc->getExpiration());
		$this->assertEquals($csc, $cc->getCSC());
	}

	public function testAnInvalidNumber()
	{
		$this->setExpectedException('InvalidArgumentException', "123 is not a valid credit card number.");

		extract( $this->getCreditCardInputs() );
		$number = '123';
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);
	}

	public function testAnInvalidExpirationMonth()
	{
		$this->setExpectedException('InvalidArgumentException', "13 is not a valid month.");

		extract( $this->getCreditCardInputs() );
		$exp_month = 13;
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);
	}

	public function testAnInvalidExpirationYear()
	{
		$this->setExpectedException('InvalidArgumentException', "2018 is not a positive integer.");

		extract( $this->getCreditCardInputs() );
		$exp_year = '2018';
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);
	}

	public function testAnInvalidExpirationYearAgain()
	{
		$this->setExpectedException('InvalidArgumentException', "2013 is not a valid year.");

		extract( $this->getCreditCardInputs() );
		$exp_year = 2013;
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);
	}

	public function testAnInvalidCSC()
	{
		$this->setExpectedException('InvalidArgumentException', "1111 is not a string.");

		extract( $this->getCreditCardInputs() );
		$csc = 1111;
		$cc = new Visa($name, $number, $exp_month, $exp_year, $csc);
	}

}