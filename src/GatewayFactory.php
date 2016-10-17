<?php
namespace NetworkForGood;

use SoapClient;
use GuzzleHttp\Client;
use NetworkForGood\Models\Partner;
use NetworkForGood\Http\SoapGateway;
use NetworkForGood\Http\JsonGateway;

class GatewayFactory
{
    private $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function getConfig()
    {
        return require 'config.php';
    }

    public static function build(array $config = [])
    {
        $config = empty($config) ? static::getConfig() : $config;

        $self = new static($config);

        if(!$config['protocol']){
          return $self->buildSOAP();
        }

        $method = sprintf("build%s", $config['protocol']);

        return $self->{$method}();
    }

    public function buildSOAP()
    {
        $config = $this->config;

        // Partner
        $partner = $this->getPartner();

        // Gateway
        $wsdl = $config['endpoints']['sandbox']['wsdl'];
        $client 	= new SoapClient($wsdl, [
          'trace' => true,
          'exceptions' => true
        ]);

        return new SoapGateway($partner, $client);
    }

    public function buildJSON()
    {
        $partner = $this->getPartner();

        $config = $this->config;

        $client = new Client($config['json']);

        return new JsonGateway($partner, $client);
    }

    private function getPartner()
    {
        $config = $this->config;

        return Partner::create([
          'PartnerID' => $config['partner']['id'],
          'PartnerPW' => $config['partner']['password'],
          'PartnerSource' => $config['partner']['source'],
          'PartnerCampaign' => $config['partner']['campaign']
        ]);
    }
}
