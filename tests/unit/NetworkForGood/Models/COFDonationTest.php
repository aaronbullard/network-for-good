<?php namespace NetworkForGood\Models;


class COFDonationTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;


	protected function _before()
	{

	}


	public function testStaticConstructor()
	{
		$makeCOFDonation = $this->tester->makeCOFDonation(3);

		$totalAmount = 0;
		foreach($makeCOFDonation->getDonationLineItems() as $donationItem)
		{
			$totalAmount += $donationItem->getItemAmount();
		}

		$this->assertEquals($totalAmount, $makeCOFDonation->getTotalAmount());
	}


	public function testOnlyDonationItemsCanBeAdded()
	{
		$donor = $this->tester->makeDonor();
		$donationItem = $this->tester->makeDonationLineItem();
		$donationLineItems = [1, 2, 3];

		$exceptionCalled = FALSE;

		try{
			$makeCOFDonation = COFDonation::create(
				'partnerTransId',
				$donor,
				1234,
				$donationLineItems
			);	
		}
		catch(\Exception $e)
		{
			$exceptionCalled = TRUE;
		}
		$this->assertTrue($exceptionCalled);
	}


	public function testArrayableInterface()
	{
		$makeCOFDonation = $this->tester->makeCOFDonation(3);

		$array = $makeCOFDonation->toArray();

		$this->assertTrue( is_array( $array ));
		$this->assertInstanceOf('NetworkForGood\\Models\\Model', $makeCOFDonation);

		foreach($array['DonationLineItems'] as $donationItemArray)
		{
			$this->assertTrue( is_array($donationItemArray));
		}
	}

}