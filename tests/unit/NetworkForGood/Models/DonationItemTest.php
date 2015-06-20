<?php namespace NetworkForGood\Models;


class DonationItemTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before()
	{
	}

	protected function _after()
	{
	}

	// tests
	public function testArrayableInterface()
	{
		$donationItem = $this->tester->makeDonationLineItem();

		$array = $donationItem->toArray();

		$this->assertTrue( is_array($array));
	}

}