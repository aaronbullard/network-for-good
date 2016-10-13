<?php
namespace NetworkForGood\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
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

    protected $count;

    public function __construct(Partner $partner, Client $client)
    {
        $this->partner = $partner;
        $this->client = $client;
        $this->count = 0;
        // Get Access Token
        $this->access_token = $this->getAccessToken();
    }

    private function getAccessToken()
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

    public function request($method, $resource, $payload = [])
    {
        if( isset($this->access_token) ){
            $payload['headers']['Authorization'] = sprintf("Bearer %s", $this->access_token);
        }

        $response = $this->client->request($method, $resource, $payload);

        $data = json_decode($response->getBody()->getContents());

        return $this->interpretResponse($data);
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
            ]
        ]);

        return new COFId($body);
    }

	public function makeCOFDonation(COFDonation $COFDonation)
    {
dd("HERE");
        $body = $this->request('POST', '/service/rest/donation', [
            'source' => $this->partner->getPartnerSource(),
            'campaign' => $this->partner->getPartnerCampaign(),
            "donationLineItems" => [
                [
                    "organizationId" => "590624430",
                    "organizationIdType" => "Ein",
                    "designation" => "Project A",
                    "dedication" => "In honor of grandma",
                    "donorPrivacy" => "ProvideAll",
                    "amount" => "12.00",
                    "feeAddOrDeduct" => "Deduct",
                    "transactionType" => "Donation",
                    "recurrence" => "NotRecurring"
                ],
                [
                    "organizationId" => "510126000486",
                    "organizationIdType" => "NcesSchoolId",
                    "designation" => "Gym",
                    "donorPrivacy" => "ProvideNameAndEmailOnly",
                    "amount" => "47.00",
                    "feeAddOrDeduct" => "Add",
                    "transactionType" => "Donation"
                ]
            ],
            "totalAmount" => 60.41,
            "tipAmount" => 0,
            "partnerTransactionId" => "1bf1c16c-fdb7-4579-abab-738dbbe852ed",
            "payment" => [
                "source" => "CreditCard",
                "donor" => [
                    "ip" => "216.7.145.0",
                    "token" => "802f365c-ed3d-4c80-8700-374aee6ac62c",
                    "firstName" => "Francis",
                   "lastName" => "Carter",
                   "email" => "FrancisGCarter@teleworm.us",
                   "phone" => "954-922-6971",
                   "billingAddress" => [
                     "street1" => "3731 Pointe Lane",
                     "city" => "Hollywood",
                     "state" => "FL",
                     "postalCode" => "33020",
                     "country" => "US"
                 ]
             ],
                 "creditCard" => [
                   "nameOnCard" => "Francis G. Carter",
                   "type" => "Visa",
                   "number" => "4111111111111111",
                   "expiration" => [
                     "month" => 11,
                     "year" => 2019
                   ],
                   "securityCode" => "123"
               ]
           ]
        ]);
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

	public function deleteDonorCOF($cofId, $donorToken = NULL){}
}
