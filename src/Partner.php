<?php namespace NetworkForGood;

class Partner implements Arrayable {

	public $id;

	public $password;

	public $source;

	public $campaign;

	public function __construct($id, $password, $source, $campaign)
	{
		$this->id 		= (string) $id;
		$this->password = (string) $password;
		$this->source 	= (string) $source;
		$this->campaign = (string) $campaign;
	}

	public function toArray()
	{
		return [
			'PartnerID'		=> $this->id,
			'PartnerPW' 	=> $this->password,
			'PartnerSource'	=> $this->source,
			'PartnerCampaign' => $this->campaign
		];
	}
}