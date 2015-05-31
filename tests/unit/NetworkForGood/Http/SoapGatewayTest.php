<?php namespace NetworkForGood\Http;

use StdClass;
use Mockery;
use SoapClient;
use NetworkForGood\Exceptions\AccessDeniedException;

class SoapGatewayTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected $partner;

	public function _before()
	{
		$this->partner = $this->tester->makePartner();
	}

	protected function _after()
	{
		Mockery::close();
	}

	protected function makeGateway(SoapClient $soapClient)
	{
		return new SoapGateway($this->partner, $soapClient);
	}

	protected function setExpectedStatusCodeException(SoapClient &$soapClient, $resultName, $params, $statusCode)
	{
		$soapClient->shouldReceive($resultName)
			->with($params)->once()
			->andReturn( $this->tester->mockResponse($resultName . 'Result', $statusCode));

		$this->setExpectedException("NetworkForGood\\Exceptions\\" . $statusCode . "Exception");
	}

	// tests
	public function testCreateCofSuccess()
	{
		$donor 	= $this->tester->makeDonor();
		$creditCard = $this->tester->makeCreditCard();
		$params = array_merge($donor->toArray(), $creditCard->toArray(), $this->partner->toArray());

		// Test Success
		$soapClient = Mockery::mock('SoapClient');
		$soapClient->shouldReceive('CreateCOF')->with($params)->once()->andReturn( $this->tester->mockResponse('CreateCOFResult'));
		$gateway = $this->makeGateway( $soapClient );
		$response = $gateway->createCOF($donor, $creditCard);

		$this->assertEquals('Success', $response->getStatusCode());
	}

	protected function testCreateCofStatusFailed($status)
	{
		$donor 		= $this->tester->makeDonor();
		$creditCard = $this->tester->makeCreditCard();
		$params = array_merge($donor->toArray(), $creditCard->toArray(), $this->partner->toArray());

		$soapClient = Mockery::mock('SoapClient');
		$gateway = $this->makeGateway( $soapClient );
		$this->setExpectedStatusCodeException($soapClient, 'CreateCOF', $params, $status);
		$response = $gateway->createCOF($donor, $creditCard);
	}

	public function testCreateCofAccessDenied()
	{
		$this->testCreateCofStatusFailed('AccessDenied');
	}

	public function testCreateCofValidationFailed()
	{
		$this->testCreateCofStatusFailed('ValidationFailed');
	}

	public function testCreateCofProcessorError()
	{
		$this->testCreateCofStatusFailed('ProcessorError');
	}

	public function testCreateCofOtherError()
	{
		$this->testCreateCofStatusFailed('OtherError');
	}

	public function testGetDonorCofsSuccess()
	{
		$params = $this->partner->toArray();
		$params['DonorToken'] = 'token';
		// Test Success
		$soapResponse = $this->tester->mockResponse('GetDonorCOFsResult');
		$soapResponse->GetDonorCOFsResult->Cards = new \StdClass();
		$soapResponse->GetDonorCOFsResult->Cards->COFRecord = [];
		$soapClient = Mockery::mock('SoapClient');
		$soapClient->shouldReceive('GetDonorCOFs')->with($params)->once()->andReturn( $soapResponse );
		$gateway = $this->makeGateway( $soapClient );
		$response = $gateway->getDonorCOFs($params['DonorToken']);

		$this->assertTrue(is_array( $response ));
	}

	protected function testGetDonorCofsStatusFailed($status)
	{
		$params = $this->partner->toArray();
		$params['DonorToken'] = 'token';

		$soapClient = Mockery::mock('SoapClient');
		$gateway = $this->makeGateway( $soapClient );
		$this->setExpectedStatusCodeException($soapClient, 'GetDonorCOFs', $params, $status);

		$response = $gateway->getDonorCOFs($params['DonorToken']);
	}

	public function testGetDonorCofsAccessDenied()
	{
		$this->testGetDonorCofsStatusFailed('AccessDenied');
	}

	public function testGetDonorCofsValidationFailed()
	{
		$this->testGetDonorCofsStatusFailed('ValidationFailed');
	}

	public function testGetDonorCofsProcessorError()
	{
		$this->testGetDonorCofsStatusFailed('ProcessorError');
	}

	public function testGetDonorCofsOtherError()
	{
		$this->testGetDonorCofsStatusFailed('OtherError');
	}

	public function testDeleteDonorCOF()
	{
		$params = $this->partner->toArray();
		$params['COFId'] = 'cofid';

		// Test Success without DonorToken
		$soapClient = Mockery::mock('SoapClient');
		$soapClient->shouldReceive('DeleteDonorCOF')->with($params)->once()->andReturn( $this->tester->mockResponse('DeleteDonorCOFResult'));
		$gateway = $this->makeGateway( $soapClient );
		$response = $gateway->deleteDonorCOF($params['COFId']);

		$this->assertTrue($response);

		// Test Success with DonorToken
		$params['DonorToken'] = 'token';
		$soapClient = Mockery::mock('SoapClient');
		$soapClient->shouldReceive('DeleteDonorCOF')->with($params)->once()->andReturn( $this->tester->mockResponse('DeleteDonorCOFResult'));
		$gateway = $this->makeGateway( $soapClient );
		$response = $gateway->deleteDonorCOF($params['COFId'], $params['DonorToken']);

		$this->assertTrue($response);
	}

	public function testMakeCOFDonation()
	{
		$transaction = $this->tester->makeDonationTransaction(0);

		$transaction->addDonationItem( $this->tester->makeDonationLineItem() );
		$transaction->addDonationItem( $this->tester->makeDonationLineItem() );
		$transaction->addDonationItem( $this->tester->makeDonationLineItem() );
		$transaction->addDonationItem( $this->tester->makeDonationLineItem() );

		$params = array_merge($this->partner->toArray(), $transaction->toArray());

		// Test Success without DonorToken
		$soapClient = Mockery::mock('SoapClient');
		$soapClient->shouldReceive('MakeCOFDonation')->with($params)->once()->andReturn( $this->tester->mockResponse('MakeCOFDonationResult'));
		$gateway = $this->makeGateway( $soapClient );
		$response = $gateway->makeCOFDonation($transaction);

		$this->assertTrue( $response );
	}

}