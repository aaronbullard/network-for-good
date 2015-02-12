<?php namespace NetworkForGood;

use NetworkForGood\CreditCards\CreditCard;

interface NetworkForGoodInterface {
	public function createCOF(Donor $donor, CreditCard $creditCard);
	public function makeCOFDonation(Transaction $transaction);
	public function getDonorCOFs($donorToken);
}