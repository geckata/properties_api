<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\GeocodePropertyAddress;
use App\Models\City;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    /**
     * @var Property
     */
    protected $property;

    /**
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $properties = $this->property
            ->with('city')
            ->select('id', 'city_id', 'name', 'address_line_1', 'address_line_2', 'postcode', 'latitude', '.longitude')
            ->orderBy('id', 'asc')
            ->simplePaginate(25);

        // Build the property collection
        $propertiesCollection = [];
        foreach ($properties as $property) {
            $propertiesCollection[] = $this->createPropertyResourceArray($property);
        }

        return response()->json([
            'resource' => 'collection',
            'collection_resource' => 'property',
            'resource_url' => request()->getUri(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'previous_page_url' => $properties->previousPageUrl(),
                'next_page_url' => $properties->nextPageUrl(),
            ],
            'collection' => $propertiesCollection,
        ]);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $property = $this->property
            ->with('city', 'attributes.name')
            ->find($id);

        // Property not found
        if (!$property) {
            return response()->json([
                'errors' => [
                    ['name' => 'resource_not_found', 'message' => 'The requested resource could not be found.'],
                ],
            ], 404);
        }

        // Response data
        $propertyResource = $this->createPropertyResourceArray($property);

        return response()->json($propertyResource);
    }

    /**
     * @param Request $request
     * @param City    $city
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, City $city)
    {
        // Validation
        $errors = $this->validateStoreRequest($request->all());

        if ($errors) {
            return response()->json([
                'errors' => [
                    ['name' => 'validation_error', 'message' => 'The submitted data could not be validated.', 'errors' => $errors],
                ],
            ], 422);
        }

        // Create the property record
        $city = $city->where('name', $request->get('city'))->first();

        $property = $this->property;
        $property->city_id = $city->id;
        $property->name = $request->get('name');
        $property->address_line_1 = $request->get('address_line_1');
        $property->address_line_2 = $request->get('address_line_2');
        $property->postcode = $request->get('postcode');
        $property->latitude = null;
        $property->longitude = null;
        $property->save();

        // Response data
        $property->setRelation('city', $city);
        $propertyResource = $this->createPropertyResourceArray($property);

        // Geocode the property address using a queued job
        dispatch(new GeocodePropertyAddress($property));

        return response()->json($propertyResource);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function validateStoreRequest(array $data): array
    {
        $validator = validator($data, [
            'name' => 'nullable|string|max:100',
            'address_line_1' => 'required|string|min:5|max:150',
            'address_line_2' => 'nullable|string|min:5|max:150',
            'postcode' => 'required|string|max:30',
            'city' => 'required|string|max:75|exists:cities,name',
        ]);

        return $validator->getMessageBag()->toArray();
    }

    /**
     * @param Property $property
     *
     * @return array
     */
    protected function createPropertyResourceArray(Property $property): array
    {
        $propertyResource = $property->only(['id', 'city_id', 'name', 'address_line_1', 'address_line_2', 'postcode', 'latitude', 'longitude']);
        $propertyResource['city_name'] = $property->city->name;
        $propertyResource['resource'] = 'property';
        $propertyResource['resource_url'] = route('properties.show', $property->id);

        // Handle the additional attributes if loaded
        if ($property->relationLoaded('attributes')) {
            // Format the property attributes in an array
            $propertyAttributes = [];
            foreach ($property->attributes as $attribute) {
                $propertyAttributes[$attribute->name->attribute] = $attribute->value;
            }

            $propertyResource['attributes'] = $propertyAttributes;
        }

        return $propertyResource;
    }
}
