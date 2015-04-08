<?php namespace NetworkForGood;

use \Codeception\Modules\UnitHelper;
use Faker\Factory as Faker;
use NetworkForGood\Transaction;

class DonationTransactionTest extends \Codeception\TestCase\Test {
	
	/**
	 * @var \UnitTester
	 */
	protected $tester;


	protected function _before()
	{

	}

	public function testTransactionWithObjects()
	{
		$transaction = $this->tester->makeDonationTransaction();
		$this->testTransaction( $transaction );
	}

	public function testTransactionWithIds()
	{
		$transaction = $this->tester->makeDonationTransactionWithIds();

		$this->testTransaction( $transaction );
	}

	protected function testTransaction(Transaction $transaction)
	{
		// Make line item
		$donations = [];
		$donations[] = $this->tester->makeDonationLineItem();
		$donations[] = $this->tester->makeDonationLineItem();
		$donations[] = $this->tester->makeDonationLineItem();

		$total_amount = 0;
		foreach( $donations as $donation)
		{
			$transaction->addDonationLineItem( $donation );
			$total_amount += $donation->getDollarAmount();
		}

		$this->assertTrue( is_array($transaction->toArray()) );
		$this->assertEquals($total_amount, $transaction->getTotalAmount());
	}
}