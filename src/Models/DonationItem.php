<?php namespace NetworkForGood\Models;


class DonationItem extends Model {

	protected static $properties = [
		"NpoEin",
		"Designation",
		"Dedication",
		"donorVis",
		"ItemAmount",
		"RecurType",
		"AddOrDeduct",
		"TransactionType"
	];

	protected static $optional = ['Designation', 'Dedication'];


	public function setItemAmount($amount)
	{
		static::validateIsFloat($amount);

		$this->attributes['ItemAmount'] = (float) $amount;

		return $this;
	}
}