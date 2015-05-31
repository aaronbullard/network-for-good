<?php namespace NetworkForGood;

use NetworkForGood\Models\Donor;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Models\Transaction;

interface NetworkForGoodInterface {
	public function createCOF(Donor $donor, CreditCard $creditCard);
	public function makeCOFDonation(Transaction $transaction);
	public function getDonorCOFs($donorToken);
}