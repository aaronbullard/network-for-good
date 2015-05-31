<?php namespace NetworkForGood\Models;


class PartnerTest extends \Codeception\TestCase\Test
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
	public function testThis()
	{
		$params = [
			'PartnerID' => '1234',
			'PartnerPW' => md5('password'),
			'PartnerSource' => 'Source',
			'PartnerCampaign' => 'Campaign'
		];

		$partner = new Partner($params);

		$this->assertEquals($params, $partner->toArray());
	}

}