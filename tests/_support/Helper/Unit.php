<?php namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

use StdClass;
use Faker\Factory as Faker;
use Faker\Provider\pl_PL\Person;
use Faker\Provider\Internet;
use NetworkForGood\Models\Donor;
use NetworkForGood\Models\Partner;
use NetworkForGood\Models\DonationItem;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Models\Transaction;
use NetworkForGood\Models\COFDonation;

use NetworkForGood\Contracts\DonorVis;
use NetworkForGood\Contracts\RecurType;
use NetworkForGood\Contracts\AddOrDeduct;
use NetworkForGood\Contracts\TransactionType;

class Unit extends \Codeception\Module
{

	public function makeDonor($successful = TRUE)
	{
		$faker = Faker::create();

		$streetAddress = $successful ? (string)$faker->numberBetween(1, 333) . $faker->streetName :
										(string)$faker->numberBetween(334, 999) . $faker->streetName;

		return new Donor([
			'DonorToken' => $faker->word,
			'DonorIpAddress' => $faker->ipv4,
			'DonorFirstName' => $faker->firstName,
			'DonorLastName' => $faker->lastName,
			'DonorEmail' => $faker->email,
			'DonorAddress1' => $streetAddress,
			// 'DonorAddress2' => "",
			'DonorCity' => $faker->city,
			'DonorState' => $faker->stateAbbr,
			'DonorZip' => $faker->randomElement(['20005', '22213', '28412']),
			'DonorCountry' => 'US',
			'DonorPhone' => $faker->phoneNumber	
		]);
	}

	public function makePartner()
	{
		$faker = Faker::create();

		return new Partner([
			'PartnerID' => $faker->word,
			'PartnerPW' => $faker->domainWord,
			'PartnerSource' => $faker->uuid,
			'PartnerCampaign' => $faker->catchPhrase
		]);
	}

	public function makeDonationLineItem()
	{
		$faker = Faker::create();
		$faker->addProvider(new Person($faker));

		return new DonationItem([
			"NpoEin" => $faker->taxpayerIdentificationNumber,
			// "Designation" => ,
			// "Dedication" => ,
			"donorVis" => DonorVis::PROVIDE_ALL,
			"ItemAmount" => $faker->numberBetween(10, 99000),
			"RecurType" => RecurType::NOT_RECURRING,
			"AddOrDeduct" => AddOrDeduct::ADD,
			"TransactionType" => TransactionType::DONATION
		]);
	}

	public function makeCreditCard($successful = TRUE)
	{
		$faker = Faker::create();
		$currYear = date('Y');

		$cvc = $successful ? $faker->numberBetween(001, 300) : $faker->numberBetween(301, 600);

		return new CreditCard([
			"NameOnCard" => $faker->name,
			"CardType" => 'Amex',
			"CardNumber" => '371449635398431',
			"ExpMonth" => $faker->numberBetween(1, 12),
			"ExpYear" => $faker->numberBetween($currYear, $currYear + 5),
			"CSC" => (string) $cvc
		]);
	}

	public function makeDonationTransaction($numOfDonations = 1)
	{
		$faker = Faker::create();

		$partnerTransactionIdentifier = $faker->md5;
		$partner = $this->makePartner();
		$donor = $this->makeDonor();
		$creditCard = $this->makeCreditCard();

		$transaction = Transaction::create($partnerTransactionIdentifier, $partner, $donor, $creditCard);

		while($numOfDonations--)
		{
			$transaction->addDonationItem( $this->makeDonationLineItem() );
		}

		return $transaction;
	}

	public function makeCOFDonation($numDonationItems = 3)
	{
		$donor = $this->makeDonor();
		$donationLineItems = [];

		for ($i=0; $i < $numDonationItems; $i++) { 
			$donationLineItems[] = $this->makeDonationLineItem();
		}

		return COFDonation::create(
			'partnerId',
			$donor,
			1234,
			$donationLineItems
		);
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
