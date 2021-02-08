<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CityTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test for fetch without token
     */

    public function testFetchCitiesWithoutToken()
    {
        $this->json('GET', 'api/cities', ['accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * Test for return cities with relationship Country
     */

    public function testFetchCitiesWithRelationship()
    {
        City::factory()->create();

        Passport::actingAs($this->user);

        $this->json('GET', 'api/cities', ['accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'cities' => [
                    '*' => [
                        'id',
                        'country_id',
                        'country' => [
                            'id',
                            'country_name',
                            'created_at',
                            'updated_at'
                        ],
                        'city_name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /**
     * Test - Response errors with validation (city_name, country)
     */

    public function testValidationErrorsShouldBeReturnedOnCreate()
    {
        Passport::actingAs($this->user);

        $this->json('POST', 'api/cities', ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'city_name' => ['The city name field is required.'],
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    /**
     * Test - Response errors with validation (country)
     * Pass country as text
     * Error Country must be type integer
     */

    public function testCountryTypeTextErrorsShouldBeReturnedOnCreate()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->user);

        $body = [
            'city_name' => 'Katowice',
            'country' => 'Poland'
        ];

        $this->json('POST', 'api/cities', $body, ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The country must be an integer.']
                ]
            ]);
    }

    /**
     * Test - Response errors with validation (country)
     * Pass country as number
     * Error Country must exist
     */

    public function testCountryTypeIntegerErrorsShouldBeReturnedOnCreate()
    {
        Passport::actingAs($this->user);

        $body = [
            'city_name' => 'Katowice',
            'country' => 1
        ];

        $this->json('POST', 'api/cities', $body, ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The selected country is invalid.']
                ]
            ]);
    }

    /**
     * Test - Pass correct data, check response
     */

    public function testCorrectResponseOnCreate() {
        Passport::actingAs($this->user);
        $country = Country::factory()->create();

        $body = [
            'city_name' => 'Warszawa',
            'country' => $country->id
        ];

        $this->json('POST', 'api/cities', $body, ['accept' => 'application/json'])
            ->assertJsonStructure([
                "city" => [
                    "id",
                    "country_id",
                    "city_name",
                    "created_at",
                    "updated_at",
                    "country" => [
                        "id",
                        "country_name",
                        "created_at",
                        "updated_at"
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    /**
     * Test - Response errors with validation (city_name, country)
     */

    public function testValidationErrorsShouldBeReturnedOnUpdate()
    {
        Passport::actingAs($this->user);

        $city = City::factory()->create();

        $this->json('PUT', route('cities.update', $city), ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'city_name' => ['The city name field is required.'],
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    /**
     * Test - Response errors with validation (country)
     * Pass country as text
     * Error Country must be type integer
     */

    public function testCountryTypeTextErrorsShouldBeReturnedOnUpdate()
    {
        $city = City::factory()->create();
        Passport::actingAs($this->user);

        $body = [
            'city_name' => 'Warszawa',
            'country' => 'Poland'
        ];

        $this->json('PUT', route('cities.update', $city), $body, ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The country must be an integer.']
                ]
            ]);
    }

    /**
     * Test - Response errors with validation (country)
     * Pass country as number
     * Error Country must exist
     */

    public function testCountryTypeIntegerErrorsShouldBeReturnedOnUpdate()
    {
        Passport::actingAs($this->user);
        $city = City::factory()->create();

        $body = [
            'city_name' => 'Warszawa',
            'country' => 1
        ];

        $this->json('PUT', route('cities.update', $city), $body, ['accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country' => ['The selected country is invalid.']
                ]
            ]);
    }

    /**
     * Test - Pass correct data, check response
     */

    public function testCorrectResponseOnUpdate() {
        Passport::actingAs($this->user);
        $city = City::factory()->create();
        $country = Country::factory()->create();

        $body = [
            'city_name' => 'Warszawa',
            'country' => $country->id
        ];

        $this->json('PUT', route('cities.update', $city), $body, ['accept' => 'application/json'])
            ->assertJsonStructure([
                "city" => [
                    "id",
                    "country_id",
                    "city_name",
                    "created_at",
                    "updated_at",
                    "country" => [
                        "id",
                        "country_name",
                        "created_at",
                        "updated_at"
                    ]
                ]
            ])
            ->assertStatus(200);
    }

    /**
     * Test for delete record
     */

    public function testDeleteRecord() {
        $city = City::factory()->create();
        Passport::actingAs($this->user);

        $this->json('DELETE', route('cities.destroy', $city), ['accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJSON([
                'message' => 'Record has been deleted.'
            ]);
    }
}
