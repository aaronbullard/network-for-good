<?php namespace NetworkForGood;

use \Codeception\Modules\UnitHelper;
use Faker\Factory as Faker;

class DonorTest extends \Codeception\TestCase\Test {
	
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected $faker;

	protected function _before()
	{
		$this->faker = Faker::create();
	}

	public function testGettingArray()
	{
		$donor = $this->tester->makeDonor();

		$this->assertTrue( is_array($donor->toArray()) );
	}
}