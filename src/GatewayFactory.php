<?php
namespace NetworkForGood;

use SoapClient;
use GuzzleHttp\Client;
use NetworkForGood\Models\Partner;
use NetworkForGood\Http\SoapGateway;
use NetworkForGood\Http\JsonGateway;

class GatewayFactory
{

    private function __construct(){}

    public static function getConfig()
    {
        return require 'config.php';
    }

    public static function build()
    {
        $config = static::getConfig();

        if(!$config['protocol']){
          return static::build();
        }

        $method = sprintf("build%s", $config['protocol']);

        return static::{$method}();
    }

    public static function buildSOAP()
    {
        $config = static::getConfig();

        // Partner
        $partner = static::getPartner();

        // Gateway
        $wsdl = $config['endpoints']['sandbox']['wsdl'];
        $client 	= new SoapClient($wsdl, [
          'trace' => true,
          'exceptions' => true
        ]);

        return new SoapGateway($partner, $client);
    }

    public static function buildJSON()
    {
        $partner = static::getPartner();

        $config = static::getConfig();

        $client = new Client($config['json']);

        return new JsonGateway($partner, $client);
    }

    private static function getPartner()
    {
        $config = static::getConfig();

        return Partner::create([
          'PartnerID' => $config['partner']['id'],
          'PartnerPW' => $config['partner']['password'],
          'PartnerSource' => $config['partner']['source'],
          'PartnerCampaign' => $config['partner']['campaign']
        ]);
    }
}
