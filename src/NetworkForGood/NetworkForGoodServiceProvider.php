<?php namespace NetworkForGood;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as Http;
use NetworkForGood\Http\Gateway;
use NetworkForGood\Http\SoapGateway;

class NetworkForGoodServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = TRUE;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->make('config')->addNamespace('NetworkForGood', __DIR__ );
		$this->registerPartner();
		$this->registerGateway();
		$this->registerSoapGateway();
		$this->registerInterface();
	}

	protected function registerPartner()
	{
		$this->app->bind('NetworkForGood\\Partner', function($app){
			$config = $app->make('config')->get('NetworkForGood::config.partner');
			return new Partner($config['id'], $config['password'], $config['source'], $config['campaign']);
		});
	}

	protected function registerGateway()
	{
		$this->app->bind('NetworkForGood\\Http\\Gateway', function($app)
		{
			// set endpoint for environment
			if( $app->environment() === 'production'){
				$config = $app->make('config')->get('NetworkForGood::config.endpoints.production');
			}else{
				$config = $app->make('config')->get('NetworkForGood::config.endpoints.sandbox');
			}
			
			$base_url 	= $config['url'];
			$http 		= new Http();
			$partner 	= $app->make('NetworkForGood\\Partner');
			return new Gateway($base_url, $http, $partner);
		});
	}

	protected function registerSoapGateway()
	{
		$this->app->bind('NetworkForGood\\Http\\SoapGateway', function($app)
		{
			// set endpoint for environment
			if( $app->environment() === 'production'){
				$config = $app->make('config')->get('NetworkForGood::config.endpoints.production');
			}else{
				$config = $app->make('config')->get('NetworkForGood::config.endpoints.sandbox');
			}
			
			$partner 	= $app->make('NetworkForGood\\Partner');
			$wsdl = $config['wsdl'];
			return new SoapGateway($partner, $wsdl);
		});
	}

	protected function registerInterface()
	{
		$this->app->bind('NetworkForGood\\NetworkForGoodInterface', 'NetworkForGood\\Http\\SoapGateway');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('aaronbullard/network-for-good');
	}

}
