<?php namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

use StdClass;
use Faker\Factory as Faker;
use Faker\Provider\pl_PL\Person;
use Faker\Provider\Internet;
use NetworkForGood\Donor;
use NetworkForGood\Partner;
use NetworkForGood\DonationLineItem;
use NetworkForGood\DonationTransaction;
use NetworkForGood\CreditCards\Amex;
use NetworkForGood\Transaction;

class Unit extends \Codeception\Module
{

	public function makeDonor($successful = TRUE)
	{
		$faker = Faker::create();

		$streetAddress = $successful ? (string)$faker->numberBetween(1, 333) . $faker->streetName :
										(string)$faker->numberBetween(334, 999) . $faker->streetName;

		return new Donor(
			$faker->firstName,
			$faker->lastName,
			$faker->email,
			$streetAddress,
			NULL,
			$faker->city,
			$faker->stateAbbr,
			$faker->randomElement(['20005', '22213', '28412']),
			'US',
			$faker->phoneNumber,
			$faker->randomNumber()
		);
	}

	public function makePartner()
	{
		$faker = Faker::create();

		return new Partner($faker->randomNumber(), $faker->domainWord, $faker->uuid, $faker->catchPhrase);
	}

	public function makeDonationLineItem()
	{
		$faker = Faker::create();
		$faker->addProvider(new Person($faker));

		return new DonationLineItem(
			$faker->taxpayerIdentificationNumber,
			'ProvideAll',
			$faker->numberBetween(10, 99000),
			'NotRecurring',
			'Add'
		);
	}

	public function makeCreditCard($successful = TRUE)
	{
		$faker = Faker::create();
		$currYear = date('Y');

		// return Test::create('Amex');

		$cvc = $successful ? $faker->numberBetween(001, 300) : $faker->numberBetween(301, 600);

		return new Amex(
			$faker->name,
			'371449635398431',
			$faker->numberBetween(0, 12),
			$faker->numberBetween($currYear, $currYear + 5),
			(string) $cvc
		);
	}

	public function makeDonationTransaction($numOfDonations = 1)
	{
		$faker = Faker::create();

		$partner = $this->makePartner();
		$donor = $this->makeDonor();
		$creditCard = $this->makeCreditCard();

		return Transaction::create($partner, $donor, $creditCard);
	}

	public function makeDonationTransactionWithIds()
	{
		$faker = Faker::create();

		$partner = $this->makePartner();
		$donorToken = $faker->domainWord;
		$cof_id = $faker->domainWord;

		return Transaction::createByIds($partner, $donorToken, $cof_id);
	}

	public function mockResponse($name, $StatusCode = 'Success', $Message = NULL, array $ErrorDetails = [], $CallDuration = 0)
	{
		$response = new StdClass;

		$response->StatusCode = $StatusCode;

		$response->Message = $Message;

		$response->ErrorDetails = $ErrorDetails;

		$response->CallDuration = $CallDuration;

		$obj = new StdClass;

		$obj->$name = $response;

		return $obj;
	}

}
