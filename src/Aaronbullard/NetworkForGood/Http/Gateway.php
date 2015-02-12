<?php namespace NetworkForGood\Http;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Exception\RequestException;
use NetworkForGood\NetworkForGoodInterface;
use NetworkForGood\Partner;
use NetworkForGood\Donor;
use NetworkForGood\CreditCards\CreditCard;
use NetworkForGood\Transaction;

class Gateway implements NetworkForGoodInterface {

	protected $base_url;
	protected $http;
	protected $partner;
	protected $transformer;

	public function __construct($base_url, Http $http, Partner $partner)
	{
		$this->base_url = $base_url;
		$this->http 	= $http;
		$this->partner 	= $partner;
	}

	public function createCOF(Donor $donor, CreditCard $creditCard)
	{
		$params 	= array_merge( $this->partner->toArray(), $donor->toArray(), $creditCard->toArray() );
		$request 	= $this->createPostRequest('CreateCOF', $params);

		return $this->getResponse( $request );
	}

	public function makeCOFDonation(Transaction $transaction)
	{
		$params 	= $transaction->toArray();
		$request 	= $this->createPostRequest('MakeCOFDonation', $params);
		return $this->getResponse( $request );
	}

	public function getDonorCOFs($donorToken)
	{
		$params 	= array_merge($this->partner->toArray(), ['DonorToken' => $donorToken]);
		$request 	= $this->createPostRequest('GetDonorCOFs', $params);
		return $this->getResponse( $request );
	}

	protected function createPostRequest($url_method, $params)
	{
		return $this->http->createRequest('POST', $this->base_url . '/' . $url_method, [
			'body'	=> $params
		]);
	}

	protected function getResponse($request)
	{
		try
		{
			$response = $this->http->send( $request )->getBody()->getContents();
			$responseObject =  (new XMLResponseTransformer( $response ))->toObject();
		}
		catch(RequestException $e) // Catch guzzle exception
		{
			$responseObject = (object) [
				'StatusCode' => 'OtherError',
				'Message' => $e->getMessage()
			];
		}
		
		// throw any exceptions if error
		ExceptionHandler::handle( $responseObject );

		return $responseObject;
	}
}