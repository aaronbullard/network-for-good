<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I
// use Faker\Factory as Faker;
// use Faker\Provider\pl_PL\Person;
// use Faker\Provider\Internet;
// use NetworkForGood\Http\SoapGateway;
// use NetworkForGood\Donor;
// use NetworkForGood\Partner;
// use NetworkForGood\DonationLineItem;
// use NetworkForGood\DonationTransaction;
// use NetworkForGood\CreditCards\Amex;
// use NetworkForGood\Transaction;

class Functional extends \Codeception\Module
{
	/*
	public function getConfig()
	{
		return require __DIR__ . '/../../../src/NetworkForGood/config.php';
	}

	public function bootstrapPartner($ID, $Password, $Source, $Campaign)
	{
		return new Partner($ID, $Password, $Source, $Campaign);
	}

	public function bootstrapSoapGateway(Partner $partner, $wsdl)
	{
		return new SoapGateway($partner, $wsdl);
	}

	public function getNetworkForGoodInterface()
	{
		$config = $this->getConfig();
		$wsdl = $config['endpoints']['sandbox']['wsdl'];
		extract( $config['partner'] );

		$partner = $this->bootstrapPartner($id, $password, $source, $campaign);

		return $this->bootstrapSoapGateway($partner, $wsdl);
	}


	public function makeDonor($successful = TRUE)
	{
		$faker = Faker::create();

		$streetAddress = $successful ? (string)$faker->numberBetween(1, 333) . " " . $faker->streetName :
										(string)$faker->numberBetween(334, 999) . " " . $faker->streetName;

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
			NULL
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

		$cvc = $successful ? (string)$faker->numberBetween(1, 300) : (string)$faker->numberBetween(301, 600);
		while( strlen($cvc) < 3 ){
			$cvc = '0' . $cvc;
		}

		return new Amex(
			$faker->name,
			'371449635398431',
			$faker->numberBetween(1, 12),
			$faker->numberBetween($currYear, $currYear + 5),
			$cvc
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
	//*/
}
