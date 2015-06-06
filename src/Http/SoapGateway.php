<?php namespace NetworkForGood\Http;

use SoapClient, SoapFault;
use NetworkForGood\NetworkForGoodInterface;
use NetworkForGood\Models\Partner;
use NetworkForGood\Models\Donor;
use NetworkForGood\Models\Transaction;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Responses\CardOnFile;
use NetworkForGood\Responses\COFId;
use NetworkForGood\Exceptions\ValidationFailedException;
use NetworkForGood\Exceptions\OtherErrorException;

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

		$response = $this->execute('CreateCOF', $params);

		return new COFId( $response );
	}


	/**
	 *  
	 * @param  Transaction $transaction [description]
	 * @return bool                   [description]
	 */
	public function makeCOFDonation(Transaction $transaction)
	{
		if( $transaction->countDonationItems() === 0 )
			throw new ValidationFailedException("Transaction has 0 Donation Items.");

		$params = $transaction->toArray();

		$response = $this->execute('MakeCOFDonation', $params);

		return $response->StatusCode === 'Success';
	}


	public function getDonorCOFs($donorToken)
	{
		$response = $this->execute('GetDonorCOFs', [
			'DonorToken' => $donorToken
		]);

		$cards = $response->Cards->COFRecord;

		// Set as array if not
		$cards = is_array($cards) ? $cards : [$cards];

		return array_map(function($record){
			return new CardOnFile( $record );
		}, $cards);
	}
	

	public function deleteDonorCOF($cofId, $donorToken = NULL)
	{
		$params = [];
		$params['COFId'] = $cofId;
		
		if( isset($donorToken) )
		{
			$params['DonorToken'] = $donorToken;
		}

		$response = $this->execute('DeleteDonorCOF', $params);

		return $response->StatusCode === 'Success';
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