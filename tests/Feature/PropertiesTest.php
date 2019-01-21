<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Property;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PropertiesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function testIndexReturnsPaginatedPropertyCollection()
    {
        $response = $this->get(route('properties.index'));

        $response->assertStatus(200)
            ->assertJson([
                'resource' => 'collection',
                'collection_resource' => 'property',
            ])
            ->assertJsonStructure([
                'resource_url',
                'pagination' => [
                    'current_page',
                    'previous_page_url',
                    'next_page_url',
                ],
                'collection' => [
                    '*' => [
                        'id',
                        'name',
                        'city_id',
                        'city_name',
                        'address_line_1',
                        'address_line_2',
                        'postcode',
                        'longitude',
                        'latitude',
                        'resource',
                        'resource_url',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testShowReturnsDetailedProperty()
    {
        $property = Property::first();

        $response = $this->get(route('properties.show', $property->id));

        $response->assertStatus(200)
            ->assertJson([
                'resource' => 'property',
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'city_id',
                'city_name',
                'address_line_1',
                'address_line_2',
                'postcode',
                'longitude',
                'latitude',
                'resource',
                'resource_url',

                'attributes',
            ]);
    }

    /**
     * @return void
     */
    public function testStoreCreatesProperty()
    {
        $city = City::first();

        $requestData = [
            'name' => 'Test Property',
            'address_line_1' => '750 Langworth Course',
            'address_line_2' => 'apt. 5',
            'postcode' => '51000',
            'city' => $city->name,
        ];

        $response = $this->post(route('properties.store'), $requestData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => $requestData['name'],
                'address_line_1' => $requestData['address_line_1'],
                'address_line_2' => $requestData['address_line_2'],
                'postcode' => $requestData['postcode'],
                'city_name' => $requestData['city'],
                'city_id' => $city->id,
            ])
            ->assertJsonStructure([
                'id',
                'city_id',
                'postcode',
                'longitude',
                'latitude',
                'resource',
                'resource_url',
            ]);
    }
}
