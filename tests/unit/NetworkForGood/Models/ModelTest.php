<?php namespace NetworkForGood\Models;

class SomeClass extends Model{}

class One extends Model {
	protected static $properties = ['Boolean', 'String', 'Integer', 'SomeClass', 'Optional'];
	protected static $propertyTypes = [
		'Integer' => 'integer',
		'Boolean' => 'boolean',
		'SomeClass' => 'NetworkForGood\\Models\\SomeClass',
	];
	protected static $optional = ['Optional'];
}

class ModelTest extends \Codeception\TestCase\Test
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

	protected function getParams()
	{
		return [
			'Boolean' => TRUE,
			'String' => 'my string',
			'Integer' => 123,
			'SomeClass' => new SomeClass
		];
	}

	// tests
	public function testToArray()
	{
		$params = $this->getParams();

		$one = new One($params);

		$params['SomeClass'] = $params['SomeClass']->toArray();
		$this->assertEquals($params, $one->toArray());
	}

	public function testArrayAccess()
	{
		$params = $this->getParams();
		$model = new One;

		foreach($params as $property => $value)
		{
			$model[$property] = $value;
		}

		$params['SomeClass'] = $params['SomeClass']->toArray();
		$this->assertEquals($params, $model->toArray());
	}

	public function testMagicMethods()
	{
		$params = $this->getParams();
		$model = new One;

		foreach($params as $property => $value)
		{
			$model->$property = $value;
		}

		$params['SomeClass'] = $params['SomeClass']->toArray();
		$this->assertEquals($params, $model->toArray());

		// Set setters
		$params = $this->getParams();
		$model = new One;

		foreach($params as $property => $value)
		{
			$method = 'set' . $property;
			$model->$method($value);
		}

		$params['SomeClass'] = $params['SomeClass']->toArray();
		$this->assertEquals($params, $model->toArray());
	}

	public function testInvalidTypes()
	{
		$model = new One;

		$this->setExpectedException('InvalidArgumentException');

		$model->Integer = '123';
	}

	public function testInvalidObjectTypes()
	{
		$model = new One;

		$this->setExpectedException('InvalidArgumentException');

		$model->SomeClass = '123';
	}

	public function testAMalformedObject()
	{
		$model = new One;
		$model->Integer = 123;

		$this->setExpectedException('RuntimeException');		
		$model->toArray();
	}

}