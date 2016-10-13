<?php namespace NetworkForGood;

use NetworkForGood\Models\Donor;

class NetworkForGoodInterfaceTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \FunctionalTester
	 */
	protected $tester;

	protected $gateway;

	protected function _before()
	{
		$this->gateway = GatewayFactory::build();
	}

	public function testCreateCOF($successful = TRUE)
	{
		$donor      = $this->tester->makeDonor($successful);
		$creditCard = $this->tester->makeCreditCard($successful);

		// Test
		$response = $this->gateway->createCOF($donor, $creditCard);

		$this->assertEquals($donor->DonorToken, $response->getDonorToken());
		$this->assertTrue(!is_null($response->getCOFId()));

		$this->assertDonorTokenGetsCards($donor);
	}

	protected function assertDonorTokenGetsCards(Donor $donor)
	{
		// Test
		$response = $this->gateway->getDonorCOFs($donor->getDonorToken());

		$this->assertTrue(is_array($response));
		foreach($response as $CardOnFile){
				$this->assertEquals(
					\NetworkForGood\Responses\CardOnFile::class,
					get_class($CardOnFile)
				);
		}

		$this->testMakeCOFDonation($donor, $response[0]->getCOFId());
	}

	private function testMakeCOFDonation(Donor $donor, $cofId)
	{
		$COFDonation = $this->tester->makeCOFDonation($donor, $cofId);

		// Test
		$response = $this->gateway->makeCOFDonation($COFDonation);
		$this->assertEquals($response->StatusCode, "Success");

		$this->testDeleteDonorCOF($donor, $cofId);
	}

	private function testDeleteDonorCOF($donor, $cofId)
	{
			$this->assertTrue(
				$this->gateway->deleteDonorCOF($cofId, $donor->getDonorToken())
			);
	}

}
