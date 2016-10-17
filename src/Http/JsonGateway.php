<?php
namespace NetworkForGood\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use NetworkForGood\NetworkForGoodInterface;
use NetworkForGood\Models\Partner;
use NetworkForGood\Models\Donor;
use NetworkForGood\Models\COFDonation;
use NetworkForGood\Models\CreditCard;
use NetworkForGood\Responses\CardOnFile;
use NetworkForGood\Responses\COFId;
use NetworkForGood\Exceptions\ValidationFailedException;
use NetworkForGood\Exceptions\OtherErrorException;

class JsonGateway implements NetworkForGoodInterface {

    protected $partner;

    protected $client;

    protected $access_token;

    public function __construct(Partner $partner, Client $client)
    {
        $this->partner = $partner;
        $this->client = $client;

        // Get Access Token
        $this->access_token = $this->requestAccessToken();
    }

    public function request($method, $resource, $payload = [])
    {
        $headers    = $this->getHeaders();
        $uri        = $this->getUri($resource, $payload);
        $body       = isset($payload['json']) ? $payload['json'] : null;

        $request    = new Request($method, $uri, $headers, json_encode($body));

        try{
            $response = $this->client->send( $request );
        }catch(ClientException $e){
            throw new OtherErrorException($e->getMessage(), $e->getCode(), $e);
        }

        $data = json_decode($response->getBody()->getContents());

        return $this->interpretResponse($data);
    }

    protected function getHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        if( isset($this->access_token) ){
            $headers['Authorization'] = sprintf("Bearer %s", $this->access_token);
        }

        return $headers;
    }

    protected function getUri($resource, $payload = [])
    {
        if(isset($payload['query'])){
            return sprintf("%s?%s", $resource, http_build_query($payload['query']));
        }

        return $resource;
    }

    protected function interpretResponse($response)
	{
		if( $response->status === 'Success'){
			return $response;
		}

        // transpose
        $response = (object)[
            'StatusCode' => $response->status,
            'Message' => $response->message,
            'ErrorDetails' => $response->errorDetails
        ];

		return ExceptionHandler::handle( $response );
	}

    protected function requestAccessToken()
    {
        $data = $this->request('POST', '/access/rest/token', [
            'json' => [
                'source' =>$this->partner->getPartnerSource(),
                'userid' => $this->partner->getPartnerID(),
                'password' => $this->partner->getPartnerPW(),
                'scope' => $this->partner->getPartnerCampaign()
            ]
        ]);

        return $data->token;
    }

    public function createCOF(Donor $donor, CreditCard $creditCard)
    {
        $body = $this->request('POST', '/service/rest/cardOnFile', [
            'json' => [
                'source' => $this->partner->getPartnerSource(),
                'campaign' => $this->partner->getPartnerCampaign(),
                'donor' => [
                    'token' =>  $donor->getDonorToken(),
                    'firstName' =>  $donor->getDonorFirstName(),
                    'lastName' =>  $donor->getDonorLastName(),
                    'email' =>  $donor->getDonorEmail(),
                    'phone' =>  $donor->getDonorPhone(),
                    'billingAddress' => [
                        'street1' => $donor->getDonorAddress1(),
                        'city' => $donor->getDonorCity(),
                        'state' => $donor->getDonorState(),
                        'postalCode' => $donor->getDonorZip(),
                        'country' => $donor->getDonorCountry()
                    ]
                ],
                'creditCard' => [
                    'nameOnCard' => $creditCard->getNameOnCard(),
                    'type' => $creditCard->getCardType(),
                    'number' => $creditCard->getCardNumber(),
                    'expiration' => [
                        'month' => $creditCard->getExpMonth(),
                        'year' => $creditCard->getExpYear()
                    ],
                    'securityCode' => $creditCard->getCSC()
                ]
            ]
        ]);

        return new COFId($body);
    }

    public function getDonorCOFs($donorToken)
    {
        $body = $this->request('GET', '/service/rest/cardOnFile', [
            'query' => [
                'source' => $this->partner->getPartnerSource(),
                'campaign' => $this->partner->getPartnerCampaign(),
                'donorToken' => $donorToken
            ]
        ]);

        $cards = is_array($body->cardsOnFile) ? $body->cardsOnFile : [];

        return array_map(function($card){
            return new CardOnFile($card);
        }, $cards);
    }

	public function makeCOFDonation(COFDonation $COFDonation)
    {
        $json = [
            'source' => $this->partner->getPartnerSource(),
            'campaign' => $this->partner->getPartnerCampaign(),
            "donationLineItems" => static::getDonationLineItems($COFDonation),
            "totalAmount" => $COFDonation->getTotalAmount(),
            "tipAmount" => $COFDonation->getTipAmount(),
            "partnerTransactionId" => $COFDonation->getPartnerTransactionIdentifier(),
            "payment" => [
                "source" => "CardOnFile",
                "donor" => [
                    "ip" => $COFDonation->getDonorIpAddress(),
                    "token" => $COFDonation->getDonorToken()
                ],
                "cardOnFileId" => $COFDonation->getCOFId()
            ]
        ];

        $body = $this->request('POST', '/service/rest/donation', [
            'json' => $json
        ]);

        return $body->status === 'Success';
    }


	public function deleteDonorCOF($cofId, $donorToken = NULL)
    {
        $body = $this->request('DELETE', 'service/rest/cardOnFile', [
            'query' => [
                'source' => $this->partner->getPartnerSource(),
                'campaign' => $this->partner->getPartnerCampaign(),
                'donorToken' => $donorToken,
                'cardOnFileId' => $cofId
            ]
        ]);

        return $body->status === 'Success';
    }

    protected static function getDonationLineItems(COFDonation $COFDonation)
    {
        return array_map(function($lineItem){
            return [
                "organizationId" => $lineItem->getNpoEin(),
                "organizationIdType" => "Ein",
                "designation" => $lineItem->getDesignation(),
                "donorPrivacy" => $lineItem->getdonorVis(),
                "amount" => sprintf("%d.00", $lineItem->getItemAmount()),
                "feeAddOrDeduct" => $lineItem->getAddOrDeduct(),
                "transactionType" => $lineItem->getTransactionType(),
                "recurrence" => $lineItem->getRecurType()
            ];
        }, $COFDonation->getDonationLineItems());
    }
}
