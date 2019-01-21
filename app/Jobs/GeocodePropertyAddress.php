<?php

namespace App\Jobs;

use App\Exceptions\GeocodeFailedException;
use App\Helpers\Geocoding\GoogleGeocodingHelper;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeocodePropertyAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Property
     */
    protected $property;

    /**
     * Create a new job instance.
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleGeocodingHelper $googleGeocodingHelper)
    {
        $address = [$this->property->address_line_1, $this->property->address_line_2, $this->property->city->name, $this->property->postcode];
        $address = array_filter($address);
        $address = implode(', ', $address);

        // Attempt to geocode the coordinates
        try {
            $addressCoordinates = $googleGeocodingHelper->geocodeAddress($address);
        } catch (GeocodeFailedException $e) {
            // Coordinates could not be fetched
            // We can also throw the exception so the job fails and can be retried
            return;
        }

        // Update the address
        $this->property->update([
            'latitude' => $addressCoordinates['lat'],
            'longitude' => $addressCoordinates['lon'],
        ]);
    }
}
