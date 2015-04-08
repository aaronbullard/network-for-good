<?php namespace NetworkForGood;


class NetworkForGoodInterfaceTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;

	protected $gateway;

	protected function _before()
	{
		$this->gateway = $this->tester->getNetworkForGoodInterface();
	}

	public function testCreateCOF($successful = TRUE)
	{
		$donor      = $this->tester->makeDonor($successful);
		$creditCard = $this->tester->makeCreditCard($successful);

		$response = $this->gateway->createCOF($donor, $creditCard);

		foreach(['DonorToken', 'CofId'] as $property)
		{
			$this->assertTrue( isset($response->$property ) );
		}

		$this->assertDonorTokenGetsCards($response->DonorToken);
	}

	public function testMakeCOFDonation()
	{
		$transaction = $this->tester->makeDonationTransactionWithIds();
		$lineItem = $this->tester->makeDonationLineItem();
		$transaction->addDonationLineItem( $lineItem );

		$response = $this->gateway->makeCOFDonation($transaction);

		foreach(['ChargeId', 'COFId'] as $property)
		{
			$this->assertTrue( isset($response->$property ) );
		}
	}

	protected function assertDonorTokenGetsCards($donorToken)
	{
		$response = $this->gateway->getDonorCOFs($donorToken);

		foreach(['DonorToken', 'Cards'] as $property)
		{
			$this->assertTrue( isset($response->$property ) );
		}
	}

}