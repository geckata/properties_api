# Howsy RESTful API

## API Endpoints

| Method        | Endpoint             | Description                 |
|---------------|----------------------|-----------------------------|
| GET           | /api/properties      | List properties (paginated) |
| GET           | /api/properties/{id} | Show property details       |
| POST          | /api/properties      | Create a new property       |

## Database Architecture

After careful consideration the database model I chose is having a single property table that contains the basic property information combined with an Entity-Attribute-Value design for the additional attributes.

This architecture was chosen based on the following assumptions:
- The most crucial information will be stored in the properties table
- The attributes will be used for additional information and will be displayed in the property details endpoint
- Additional basic attributes (e.g. address) will be added seldom and they can be added to the properties table

These assumptions helped shape a more detailed technical requirements specification and they play a key role in deciding the database architecture. Should they be different, the database architecture could change as well.

The database consists of 4 tables:
- cities
- properties
- property_attribute_names
- property_attribute_values

## Installation

After installing the dependencies (`composer install`) and configuring the application using the .env file (run `cp .env.example .env` and edit the file), simply run `php artisan migrate && php artisan db:seed`.

## Testing

Running the tests requires the testing database (DB_DATABASE_TESTING) to be configured in the .env file.

After the database has been configured, run `php artisan migrate --database=mysql_testing && php artisan db:seed --database=mysql_testing` to seed the testing database.

You can now run the tests by running PHPUnit.


## Notes

1. The design of the API and the database has been chosen based on a few assumptions that if changed can influence the design decisions as well.
2. The create property endpoints creates a property and queues a job for the geocoding of the property address. This approach was chosen based on the assumption that there should not be an added delay because of a synchronous request for the address geocoding when creating a property record.
3. The additional attributes are only displayed in the show property endpoint.
4. This API can be improved (see ToDo).

## ToDo

This is a list of things that can be further improved:

- The geocoding classes can be extracted to a package/library and can be further decoupled by implementing interfaces and allowing the use of different HTTP clients and geocoding services.
- The GeocodeFailedException in the GeocodePropertyAddress class can be thrown so the job fails and can be retried by the queue workers.
- More tests can be added so that validation, geocoding and additional attributes can be tested as well.
- Adding API endpoints for the cities and creating properties by passing the city id instead of the city name
