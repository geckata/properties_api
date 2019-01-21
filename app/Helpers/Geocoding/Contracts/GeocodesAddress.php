<?php

namespace App\Helpers\Geocoding\Contracts;

use App\Exceptions\GeocodeFailedException;

interface GeocodesAddress
{
    /**
     * @param string $address
     *
     * @throws GeocodeFailedException
     *
     * @return array
     */
    public function geocodeAddress(string $address): array;
}
