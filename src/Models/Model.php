<?php namespace NetworkForGood\Models;

use ArrayAccess, Iterator;
use NetworkForGood\Contracts\Arrayable;
use InvalidArgumentException, BadMethodCallException, RuntimeException;

abstract class Model implements Arrayable, ArrayAccess, Iterator {

	protected static $properties = [];

	protected static $propertyTypes = [];

	protected static $optional = [];

	protected $attributes = [];


	public function __construct(array $attributes = [])
	{
		$this->fill( $attributes );
	}


	public static function create(array $attributes)
	{
		return new static( $attributes );
	}


	public function fill(array $attributes)
	{
		foreach($attributes as $property => $value)
		{
			$method = 'set' . $property;

			$this->$method( $value );
		}

		return $this;
	}


	public function toArray()
	{
		$this->validateObjectIsFilled();

		$array = $this->attributes;

		return array_map(function($item) {
			return is_a($item, 'NetworkForGood\\Contracts\\Arrayable') ? $item->toArray() : $item;
		}, $array);
	}

	protected function merge(Arrayable $object)
	{
		$this->attributes = array_merge($this->attributes, $object->toArray());

		return $this;
	}


	protected function validateObjectIsFilled()
	{
		foreach(static::$properties as $property)
		{
			if( ! isset($this->attributes[$property]) && ! in_array($property, static::$optional))
				throw new RuntimeException(get_class($this) . "::$$property is not properly formed.");
		}
	}


	protected static function validateProperty($property)
	{
		if( ! in_array($property, static::$properties))
		{
			throw new InvalidArgumentException("$property is not a property of " . get_called_class());
		}
	}


	protected static function validatePropertyValue($property, $value)
	{
		$type = isset(static::$propertyTypes[$property]) ? 
					static::$propertyTypes[$property] : 'string';

		if( gettype($value) === $type)
			return;

		// accept an integer for type float, double, or decimal
		if( gettype($value) === 'integer' && in_array($type, ['float', 'double', 'decimal']))
			return;

		if( gettype($value) === 'object')
		{
			if( is_a($value, $type))
				return;
		}

		throw new InvalidArgumentException("$property is of type " . gettype($value) . " not of type $type.");
	}


	public static function validateIsFloat($value)
	{
		if( is_float($value))
			return TRUE;

		if( is_integer($value))
			return TRUE;

		throw new InvalidArgumentException("$value is not of type float.");
	}

	// Magic methods
	public function __call($method, $arguments)
	{
		$call = substr($method, 0, 3);
		$property = substr($method, 3);

		static::validateProperty( $property );

		if( $call === 'get'){
			return $this->attributes[$property];
		}
		
		if( $call === 'set'){
			$value = $arguments[0];
			static::validatePropertyValue($property, $value);
			$this->attributes[$property] = $value;
			return $this;
		}

		throw new BadMethodCallException("Method does not exist on the class.");
	}

	public function __set($property, $value)
	{
		$method = 'set' . $property;

		return $this->$method( $value );
	}

	public function __get($property)
	{
		$method = 'get' . $property;
		
		return $this->$method();
	}
	


	// ArrayAccess
	protected static function offsetToProperty($offset)
	{
		return is_integer($offset) ? static::$properties[$offset] : $offset;
	}


	public function offsetSet($offset, $value)
	{
		$property = static::offsetToProperty( $offset );

		$method = 'set' . $property;

		return $this->$method($value);
	}


	public function offsetExists($offset)
	{
		$property = static::offsetToProperty( $offset );

		return isset( $this->attributes[$property] );
	}


	public function offsetUnset($offset) 
	{
		$property = static::offsetToProperty( $offset );

		unset( $this->attributes[$property] );
	}


	public function offsetGet($offset)
	{
		$property = static::offsetToProperty( $offset );
		
		$method = 'get' . $property;

		return $this->$method();
	}


	// Iterator
	public function rewind()
	{
		return reset( $this->attributes );
	}

	public function current()
	{
		return current( $this->attributes );
	}

	public function key()
	{
		return key( $this->attributes );
	}

	public function next()
	{
		return next( $this->attributes );
	}
	
	public function valid()
	{
		return (key( $this->attributes ) !== NULL);
	}
}