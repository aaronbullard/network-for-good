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

	protected $wsdl;
	protected $partner;
	protected $client;

	public function __construct(Partner $partner, $wsdl)
	{
		$this->partner 	= $partner;
		$this->wsdl = $wsdl;
		$this->client = new SoapClient($wsdl, [
			'trace' => true,
			'exceptions' => true
		]);
	}

	/**
	 * @param  Donor      $donor      [description]
	 * @param  CreditCard $creditCard [description]
	 * @return [type]                 [description]
	 */
	public function createCOF(Donor $donor, CreditCard $creditCard)
	{
		try{
			$params = array_merge($donor->toArray(), $creditCard->toArray(), $this->partner->toArray());

			$response = $this->client->CreateCOF($params);

			return $this->interpretResponse( $response->CreateCOFResult );
		}
		catch(SoapFault $e)
		{
			throw new OtherErrorException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 *  
	 * @param  Transaction $transaction [description]
	 * @return [type]                   [description]
	 */
	public function makeCOFDonation(Transaction $transaction)
	{
		if( $transaction->countDonationLineItems() === 0 )
		{
			throw new ValidationFailedException("Transaction has 0 Donoation Line Items.");
		}

		try{
			$params = array_merge($transaction->toArray(), $this->partner->toArray());

			$response = $this->client->MakeCOFDonation( $params );

			return $this->interpretResponse( $response->MakeCOFDonationResult );
		}
		catch(SoapFault $e)
		{
			throw new OtherErrorException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function getDonorCOFs($donorToken)
	{
		try{
			$params = $this->partner->toArray();
			$params['DonorToken'] = $donorToken;

			$response = $this->client->GetDonorCOFs( $params );

			return $this->interpretResponse( $response->GetDonorCOFsResult );
		}
		catch(SoapFault $e)
		{
			throw new OtherErrorException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	public function deleteDonorCOF($cofId, $donorToken = NULL)
	{
		try{
			$params = $this->partner->toArray();
			$params['COFId'] = $cofId;
			
			if( isset($donorToken) )
			{
				$params['DonorToken'] = $donorToken;
			}

			$response = $this->client->DeleteDonorCOF( $params );

			return $this->interpretResponse( $response->DeleteDonorCOFResult );
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
