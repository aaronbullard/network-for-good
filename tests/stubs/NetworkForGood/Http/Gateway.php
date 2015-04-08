<?php namespace Stubs\NetworkForGood\Http;

use NetworkForGood\NetworkForGoodInterface;
use NetworkForGood\Donor;
use NetworkForGood\CreditCards\CreditCard;
use NetworkForGood\Transaction;

class Gateway implements NetworkForGoodInterface {

	protected $exception;

	public function setException($exception)
	{
		$this->exception = $exception;
	}

	private function execute()
	{
		if( isset( $this->exception) )
		{
			throw $this->exception;
		}

		return TRUE;
	}

	public function createCOF(Donor $donor, CreditCard $creditCard)
	{
		return $this->execute();
	}

	public function makeCOFDonation(Transaction $transaction)
	{
		return $this->execute();
	}

	public function getDonorCOFs($donorToken)
	{
		return $this->execute();
	}

}