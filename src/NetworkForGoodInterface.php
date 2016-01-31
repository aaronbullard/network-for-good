<?php namespace NetworkForGood;

use NetworkForGood\Models\Donor;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Models\COFDonation;

interface NetworkForGoodInterface {
	public function createCOF(Donor $donor, CreditCard $creditCard);
	public function makeCOFDonation(COFDonation $COFDonation);
	public function getDonorCOFs($donorToken);
	public function deleteDonorCOF($cofId, $donorToken = NULL);
}
