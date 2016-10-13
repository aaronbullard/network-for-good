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

        // Get Access Token
        $this->access_token = $this->requestAccessToken();
    }

    private function requestAccessToken()
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

	public function makeCOFDonation(COFDonation $COFDonation)
    {
        $body = $this->request('POST', '/service/rest/donation', [
            'json' => [
                'source' => $this->partner->getPartnerSource(),
                'campaign' => $this->partner->getPartnerCampaign(),
                "donationLineItems" => static::getDonationLineItems($COFDonation->getDonationLineItems()),
                "totalAmount" => $COFDonation->getTotalAmount(),
                "tipAmount" => $COFDonation->getTipAmount(),
                "partnerTransactionId" => $COFDonation->getPartnerTransactionIdentifier(),
                "payment" => [
                    "source" => "CreditCard",
                    "donor" => [
                        "ip" => $COFDonation->getDonorIpAddress(),
                        "token" => $COFDonation->getDonorToken()
                    ],
                    "cardOnFileId" => $COFDonation->getCOFId()
                ]
            ]
        ]);

        return $body->status === 'Success';
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

	public function deleteDonorCOF($cofId, $donorToken = NULL)
    {
        $body = $this->request('DELETE', 'service/rest/cardOnFile', [
            'json' => [
                'source' => $this->partner->getPartnerSource(),
                'campaign' => $this->partner->getPartnerCampaign(),
                'donorToken' => $donorToken,
                'cardOnFileId' => $cofId
            ]
        ]);

        return $body->status === 'Success';
    }

    public static function getDonationLineItems(COFDonation $COFDonation)
    {
        return array_map(function($lineItem){
            return [
                "organizationId" => $lineItem->getNpoEin(),
                "organizationIdType" => "Ein",
                "designation" => $lineItem->getDesignation(),
                "donorPrivacy" => $lineItem->getDonorVis(),
                "amount" => $lineItem->getItemAmount(),
                "feeAddOrDeduct" => $lineItem->getAddOrDeduct(),
                "transactionType" => $lineItem->getTransactionType(),
                "recurrence" => $lineItem->getRecurType()
            ];
        }, $COFDonation->getDonationLineItems());
    }
}
