<?php namespace NetworkForGood;

use InvalidArgumentException;

class Donor implements Arrayable {

	use ValidationTrait;

	protected $firstName;

	protected $lastName;

	protected $email;

	protected $address_1;

	protected $address_2;

	protected $city;

	protected $state;

	protected $zipcode;

	protected $country;

	protected $phone;
	
	// Partner defined id for donor
	protected $token;

	public function __construct($firstName, $lastName, $email, $address_1, $address_2 = NULL, $city, $state, $zipcode, $country, $phone, $token)
	{
		$this->firstName = (string) $firstName;
		$this->lastName = (string) $lastName;
		$this->email = (string) $email;
		$this->address_1 = (string) $address_1;
		$this->address_2 = (string) $address_2;
		$this->city = (string) $city;
		$this->state = (string) $state;
		$this->zipcode = (string) $zipcode;
		$this->country = $country;
		$this->phone = (string) $phone;
		$this->token = $token;
	}

	public function getFirstName()
	{
		return $this->firstName;
	}

	public function getLastName()
	{
		return $this->lastName;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getAddress1()
	{
		return $this->address_1;
	}

	public function getAddress2()
	{
		return $this->address_2;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function getState()
	{
		return $this->state;
	}

	public function getZipcode()
	{
		return $this->zipcode;
	}

	public function getCountry()
	{
		return $this->country;
	}

	public function getPhone()
	{
		return $this->phone;
	}
	
	public function getDonorToken()
	{
		return $this->token;
	}

	public function toArray()
	{
		$obj = [
			'DonorToken'	=> $this->token,
			'DonorFirstName'=> $this->firstName,
			'DonorLastName'	=> $this->lastName,
			'DonorEmail'	=> $this->email,
			'DonorAddress1'	=> $this->address_1,
			'DonorAddress2'	=> $this->address_2,
			'DonorCity'		=> $this->city,
			'DonorState'	=> $this->state,
			'DonorZip'		=> $this->zipcode,
			'DonorCountry'	=> $this->country,
			'DonorPhone'	=> $this->phone
		];

		if( isset($this->address_2))
		{
			$obj['DonorAddress2'] = $this->address_2;
		}

		return $obj;
	}
}
