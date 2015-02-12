<?php

return [
	'partner'	=> [
		'id' 		=> getenv('NFG_PARTNER_ID'),
		'password'	=> getenv('NFG_PARTNER_PASSWORD'),
		'source'	=> getenv('NFG_PARTNER_SOURCE'),
		'campaign'	=> getenv('NFG_PARTNER_CAMPAIGN')
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