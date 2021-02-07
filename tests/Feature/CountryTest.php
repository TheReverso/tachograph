<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * A test for try fetch countries without token.
     *
     * @return void
     */

    public function testCountriesCannotBeFetchedWithoutToken()
    {
        $this->json('GET', 'api/countries', ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }


    /**
     * A test for try fetch countries with token
     *
     * @return void
     */

    public function testFetchFreights()
    {
        Passport::actingAs($this->user);
        Country::factory()->create();

        $this->json('GET', 'api/countries', ['Acccept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'countries' => [
                    '*' => [
                        'id',
                        'country_name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function testValidationErrorsShouldBeReturnedOnCreate()
    {
        Passport::actingAs($this->user);

        $this->json('POST', 'api/countries', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country_name' => ['The country name field is required.'],
                ]
            ]);
    }

    /**
     * Test to check unique fields freight name and freight speditor name
     */

    public function testCountryNameShouldBeUnique()
    {
        $country = Country::factory()->create();

        $body = [
            'country_name' => $country->country_name,
        ];

        Passport::actingAs($this->user);

        $this->json('POST', 'api/countries', $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country_name' => ['The country name has already been taken.'],
                ]
            ]);
    }

    /**
     * Test to check correct response from add new country
     */

    public function testCorrectResponseOnStoreNewCountry()
    {
        Passport::actingAs($this->user);

        $body = [
            'country_name' => 'Poland',
        ];

        $this->json('POST', 'api/countries', $body, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'country' => [
                    'country_name',
                    'id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test which should response validation errors
     */

    public function testValidationErrorsShouldBeReturnedOnUpdate()
    {
        Passport::actingAs($this->user);
        $country = Country::factory()->create();

        $body = [
            'country_name' => '',
        ];

        $this->putJson(route('countries.update', $country), $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country_name' => ['The country name field is required.'],
                ]
            ]);
    }

    /***
     * Test for check unique fields on update
     */

    public function testFreightNameAndFreightSpeditorNameShouldBeUniqueOnUpdate()
    {
        $country = Country::factory()->create();
        $country2 = Country::factory()->create();

        $body = [
            'country_name' => $country->country_name
        ];

        Passport::actingAs($this->user);

        $this->json('PUT', route('countries.update', $country2), $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'country_name' => ['The country name has already been taken.'],
                ]
            ]);
    }

    /**
     * Test success update record
     */

    public function testCorrectResponseOnUpdate()
    {
        Passport::actingAs($this->user);
        $country = Country::factory()->create();

        $body = [
            'country_name' => 'Poland',
        ];

        $this->putJson(route('countries.update', $country), $body, ['Accept' => 'application/json'])
            ->assertStatus(200);
    }

    /**
     * Test for check allow delete record
     */

    public function testAllowDeleteFreigt()
    {
        $country = Country::factory()->create();
        Passport::actingAs($this->user);

        $this->json('DELETE', route('countries.destroy', $country->id), ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Record has been deleted.'
            ]);

        $this->assertDatabaseMissing('freights', ['id' => $country->id]);
    }
}
