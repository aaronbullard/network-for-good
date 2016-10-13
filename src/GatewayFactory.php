<?php namespace NetworkForGood;

use SoapClient;
use NetworkForGood\Models\Partner;
use NetworkForGood\Http\SoapGateway;

class GatewayFactory {

  private function __construct(){}

  public static function build()
  {
    $config = static::getConfig();

    // Partner
    $cred = $config['partner'];
    $partner = Partner::create([
      'PartnerID' => $cred['id'],
      'PartnerPW' => $cred['password'],
      'PartnerSource' => $cred['source'],
      'PartnerCampaign' => $cred['campaign']
    ]);

    // Gateway
    $wsdl = $config['endpoints']['sandbox']['wsdl'];
    $client 	= new SoapClient($wsdl, [
      'trace' => true,
      'exceptions' => true
    ]);

    return new SoapGateway($partner, $client);
  }

  public static function getConfig()
  {
      return require 'config.php';
  }
}
