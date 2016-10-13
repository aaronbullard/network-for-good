<?php

return [
	'partner'	=> [
		'id' 		=> $_ENV['NFG_PARTNER_ID'],
		'password'	=> $_ENV['NFG_PARTNER_PASSWORD'],
		'source'	=> $_ENV['NFG_PARTNER_SOURCE'],
		'campaign'	=> $_ENV['NFG_PARTNER_CAMPAIGN']
	],
	'endpoints'	=> [
		'sandbox' => [
			'url' 	=> 'https://api-sandbox.networkforgood.org/PartnerDonationService/DonationServices.asmx',
			'wsdl' 	=> 'https://api-sandbox.networkforgood.org/PartnerDonationService/DonationServices.asmx?wsdl'
		],
		'production' => [
			'url' 	=> 'https://api.networkforgood.org/PartnerDonationService/DonationServices.asmx',
			'wsdl' 	=> 'https://api.networkforgood.org/PartnerDonationService/DonationServices.asmx?wsdl'
		]
	]
];
