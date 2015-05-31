<?php namespace NetworkForGood\Models;

class Donor extends Model {

	protected static $properties = [
		'DonorToken',
		'DonorIpAddress',
		'DonorFirstName',
		'DonorLastName',
		'DonorEmail',
		'DonorAddress1',
		'DonorAddress2',
		'DonorCity',
		'DonorState',
		'DonorZip',
		'DonorCountry',
		'DonorPhone'	
	];

	protected static $optional = ['DonorAddress2'];
}