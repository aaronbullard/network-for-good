<?php namespace NetworkForGood\Http;

use SoapClient, SoapFault;
use NetworkForGood\NetworkForGoodInterface;
use NetworkForGood\Partner;
use NetworkForGood\Donor;
use NetworkForGood\CreditCards\CreditCard;
use NetworkForGood\Transaction;
use NetworkForGood\Exceptions\OtherErrorException;
use NetworkForGood\Exceptions\ValidationFailedException;

class SoapGateway implements NetworkForGoodInterface {

	protected $partner;
	protected $client;

	public function __construct(Partner $partner, SoapClient $client)
	{
		$this->partner = $partner;
		$this->client = $client;
	}

	/**
	 * @param  Donor      $donor      [description]
	 * @param  CreditCard $creditCard [description]
	 * @return [type]                 [description]
	 */
	public function createCOF(Donor $donor, CreditCard $creditCard)
	{
		$params = array_merge($donor->toArray(), $creditCard->toArray());

		return $this->execute('CreateCOF', $params);
	}

	/**
	 *  
	 * @param  Transaction $transaction [description]
	 * @return [type]                   [description]
	 */
	public function makeCOFDonation(Transaction $transaction)
	{
		if( $transaction->countDonationLineItems() === 0 )
			throw new ValidationFailedException("Transaction has 0 Donoation Line Items.");

		$params = $transaction->toArray();

		return $this->execute('MakeCOFDonation', $params);
	}

	public function getDonorCOFs($donorToken)
	{
		return $this->execute('GetDonorCOFs', [
			'DonorToken' => $donorToken
		]);
	}
	
	public function deleteDonorCOF($cofId, $donorToken = NULL)
	{
		$params = [];
		$params['COFId'] = $cofId;
		
		if( isset($donorToken) )
		{
			$params['DonorToken'] = $donorToken;
		}

		return $this->execute('DeleteDonorCOF', $params);
	}

	public function getDonorDonationHistory($donorToken)
	{
		return $this->execute('GetDonorDonationHistory', [
			'DonorToken' => $donorToken
		]);
	}

	protected function execute($method, array $params)
	{
		try{
			$partner = $this->partner->toArray();

			$body = array_merge($partner, $params);

			$response = $this->client->$method( $body );

			$index = $method . "Result";

			return $this->interpretResponse( $response->$index );
		}
		catch(SoapFault $e)
		{
			throw new OtherErrorException($e->getMessage(), $e->getCode(), $e);
		}
	}

	protected function interpretResponse($response)
	{
		if( $response->StatusCode === 'Success')
		{
			return $response;
		}

		return ExceptionHandler::handle( $response );
	}

}