<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I
use Faker\Factory as Faker;
use Faker\Provider\pl_PL\Person;
use Faker\Provider\Internet;
use NetworkForGood\Http\SoapGateway;
use NetworkForGood\Models\Donor;
use NetworkForGood\Models\Partner;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Models\COFDonation;
use NetworkForGood\Models\DonationItem;

class Functional extends \Codeception\Module
{

	public function makeDonor($successful = TRUE)
	{
		$faker = Faker::create();

		return new Donor([
			'DonorToken' => '802f365c-ed3d-4c80-8700-374aee6ac62c',
			'DonorIpAddress' => $faker->ipv4,
			'DonorFirstName' => $faker->firstName,
			'DonorLastName' => $faker->lastName,
			'DonorEmail' => $faker->email,
			'DonorAddress1' => $successful ? '222 Pass St.' : '444 Fail St.',
			'DonorCity' => $faker->city,
			'DonorState' => $faker->stateAbbr,
			'DonorZip' => $successful ? '00000' : '77777',
			'DonorCountry' => 'US',
			'DonorPhone'=> $faker->phoneNumber,
		]);
	}

	public function makeCreditCard($successful = TRUE)
	{
		$faker = Faker::create();
		$currYear = date('Y');

		$cvc = $successful ? (string)$faker->numberBetween(1, 300) : (string)$faker->numberBetween(301, 600);
		while( strlen($cvc) < 3 ){
			$cvc = '0' . $cvc;
		}

		return new CreditCard([
			'CardType'			=> 'Amex',
			'NameOnCard'	=> $faker->name,
			'CardNumber' => '371449635398431',
			'ExpMonth'		=> $faker->numberBetween(1, 12),
			'ExpYear'		=> $faker->numberBetween($currYear, $currYear + 5),
			'CSC'		=> $cvc
		]);
	}

	public function makeCOFDonation(Donor $donor, $cofId)
	{
		$donationItems = [$this->makeDonationItem()];
		$tipAmount = 0;
		return COFDonation::factory(
				"BetYourCharity",
				$donor,
				$cofId,
				$donationItems,
				$tipAmount);
	}

	public function makeDonationItem()
	{
		return new DonationItem([
			"NpoEin" => "590624430",
			"Designation" => "BetYourCharity",
			"Dedication" => "My team won the superbowl",
			"donorVis" => "ProvideAll",
			"ItemAmount" => 12.00,
			"RecurType" => "NotRecurring",
			"AddOrDeduct" => "Deduct",
			"TransactionType" => "Donation"
		]);
	}
}
