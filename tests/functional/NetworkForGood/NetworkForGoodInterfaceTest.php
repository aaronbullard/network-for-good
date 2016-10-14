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
		$config = require __DIR__ . '/../../../src/config.php';
		// $config['json']['base_uri'] = 'https://private-anon-853707d341-networkforgoodapi.apiary-proxy.com';
		$config['json']['base_uri'] = 'https://api-sandbox.networkforgood.org';
		$this->gateway = GatewayFactory::build($config);
	}

	public function testCreateCOF($successful = TRUE)
	{
		$donor      = $this->tester->makeDonor($successful);
		$creditCard = $this->tester->makeCreditCard($successful);

		// Test
		$response = $this->gateway->createCOF($donor, $creditCard);

		// $this->assertEquals($donor->getDonorToken(), $response->getDonorToken());
		$this->assertEquals($response->getStatusCode(), "Success");
		$this->assertFalse(is_null($response->getCOFId()));
	}

	public function testDonorTokenGetsCards()
	{
		$this->testCreateCOF();
		$donor = $this->tester->makeDonor();

		$response = $this->gateway->getDonorCOFs($donor->getDonorToken());

		$this->assertTrue(is_array($response));
		foreach($response as $CardOnFile){
				$this->assertEquals(
					\NetworkForGood\Responses\CardOnFile::class,
					get_class($CardOnFile)
				);
		}

		return $response;
	}

	public function testMakeCOFDonation()
	{
		$donor = $this->tester->makeDonor();
		$cards = $this->gateway->getDonorCOFs($donor->getDonorToken());
		$this->assertTrue(count($cards) > 0);
		$COFDonation = $this->tester->makeCOFDonation($donor, $cards[0]->getCOFId());

		// Test
		$response = $this->gateway->makeCOFDonation($COFDonation);
		$this->assertTrue($response);
	}

	public function testDeleteDonorCOF()
	{
		$donor = $this->tester->makeDonor();

		$cards = $this->gateway->getDonorCOFs($donor->getDonorToken());
		$card = $cards[0];

		$this->assertTrue(
			$this->gateway->deleteDonorCOF($card->getCOFId(), $donor->getDonorToken())
		);
	}

}
