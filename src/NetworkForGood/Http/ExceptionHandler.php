<?php namespace NetworkForGood\Http;

use NetworkForGood\Exceptions\AccessDeniedException;
use NetworkForGood\Exceptions\ChargeFailedException;
use NetworkForGood\Exceptions\OtherErrorException;
use NetworkForGood\Exceptions\ProcessorErrorException;
use NetworkForGood\Exceptions\SystemErrorException;
use NetworkForGood\Exceptions\TPCFeeMinimumCapCheckFailedException;
use NetworkForGood\Exceptions\ValidationFailedException;

class ExceptionHandler {

	public static function handle($response)
	{
		$statusCode = $response->StatusCode;

		if( $statusCode === 'Success' )
		{
			return TRUE;
		}

		$exceptionClassName = 'NetworkForGood\\Exceptions\\' . $statusCode . 'Exception';

		$exception = new $exceptionClassName($response->Message);

		$exception->setResponse( $response );

		if ( isset( $response->ErrorDetails))
		{
			$exception->setErrorDetails($response->ErrorDetails);
		}
		
		throw $exception;
	}
}