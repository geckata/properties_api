<?php

declare(strict_types=1);

namespace App\Helpers\Geocoding;

use App\Exceptions\GeocodeFailedException;
use App\Helpers\Geocoding\Contracts\GeocodesAddress;
use GuzzleHttp\Client;

class GoogleGeocodingHelper implements GeocodesAddress
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $address
     *
     * @throws GeocodeFailedException
     *
     * @return array
     */
    public function geocodeAddress(string $address): array
    {
        $requestUrl = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.env('GOOGLE_GEOCODING_API_KEY');

        // Make the request and decode the response
        $geocodeResponse = $this->httpClient->get($requestUrl)->getBody();
        $geocodeData = json_decode((string) $geocodeResponse, true);

        // Extract the coordinates if the address was found
        $coordinates = [];
        if (isset($geocodeData['results'][0]['geometry']['location']['lat'])) {
            $coordinates['lat'] = $geocodeData['results'][0]['geometry']['location']['lat'];
            $coordinates['lng'] = $geocodeData['results'][0]['geometry']['location']['lng'];
        } else {
            throw new GeocodeFailedException(sprintf('Request failed with status %s', $geocodeData['status']));
        }

        return $coordinates;
    }
}
