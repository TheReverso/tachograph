<?php

namespace Tests\Feature;

use App\Models\Freight;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FreightTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * A test for try fetch freights without token.
     *
     * @return void
     */

    public function testFreightsCannotBeFetchedWithoutToken()
    {
        $this->json('GET', 'api/freights', ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * A test for try fetch freights with token
     *
     * @return void
     */

    public function testFetchFreights()
    {
        Passport::actingAs($this->user);

        $this->json('GET', 'api/freights', ['Acccept' => 'application/json'])
            ->assertStatus(200)
            ->getContent();
    }

    /**
     * A test for validation new freight
     */

    public function testValidationErrorsShouldBeReturnedOnCreate()
    {
        Passport::actingAs($this->user);

        $this->json('POST', 'api/freights', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'freight_name' => ['The freight name field is required.'],
                    'freight_speditor_name' => ['The freight speditor name field is required.'],
                    'freight_weights' => ['The freight weights field is required.']
                ]
            ]);
    }

    /**
     * Test to check unique fields freight name and freight speditor name
     */

    public function testFreightNameAndFreightSpeditorNameShouldBeUnique()
    {
        $freight = Freight::factory()->create();

        $body = [
            'freight_name' => $freight->freight_name,
            'freight_speditor_name' => $freight->freight_speditor_name,
            'freight_weights' => '1, 3'
        ];

        Passport::actingAs($this->user);

        $this->json('POST', 'api/freights', $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'freight_name' => ['The freight name has already been taken.'],
                    'freight_speditor_name' => ['The freight speditor name has already been taken.'],
                ]
            ]);
    }

    /**
     * Test to check correct response from add new freight
     */

    public function testCorrectResponseOnStoreNewFreight()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->user);

        $body = [
            'freight_name' => 'Apples',
            'freight_speditor_name' => 'apples',
            'freight_weights' => '10T, 30T'
        ];

        $this->json('POST', 'api/freights', $body, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'freight' => [
                    'id',
                    'freight_name',
                    'freight_speditor_name',
                    'freight_weights',
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
        $this->withoutExceptionHandling();
        Passport::actingAs($this->user);
        $freight = Freight::factory()->create();

        $body = [
            'freight_name' => '',
            'freight_speditor_name' => '',
            'freight_weights' => ''
        ];

        $this->putJson(route('freights.update', $freight), $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'freight_name' => ['The freight name field is required.'],
                    'freight_speditor_name' => ['The freight speditor name field is required.'],
                    'freight_weights' => ['The freight weights field is required.']
                ]
            ]);
    }

    /***
     * Test for check unique fields on update
     */

    public function testFreightNameAndFreightSpeditorNameShouldBeUniqueOnUpdate()
    {
        $freight = Freight::factory()->create();
        $freight2 = Freight::factory()->create();

        $body = [
            'freight_name' => $freight->freight_name,
            'freight_speditor_name' => $freight->freight_speditor_name,
            'freight_weights' => '1, 3'
        ];

        Passport::actingAs($this->user);

        $this->json('PUT', route('freights.update', $freight2), $body, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'freight_name' => ['The freight name has already been taken.'],
                    'freight_speditor_name' => ['The freight speditor name has already been taken.'],
                ]
            ]);
    }

    /**
     * Test success update record
     */

     public function testCorrectResponseOnUpdate() {
        Passport::actingAs($this->user);
        $freight = Freight::factory()->create();

        $body = [
            'freight_name' => 'Apples',
            'freight_speditor_name' => $freight->freight_speditor_name,
            'freight_weights' => $freight->freight_weights
        ];

        $this->putJson(route('freights.update', $freight), $body, ['Accept' => 'application/json'])
            ->assertStatus(200);
     }

    /**
     * Test for check allow delete record
     */

    public function testAllowDeleteFreigt()
    {
        $freight = Freight::factory()->create();
        Passport::actingAs($this->user);

        $this->json('DELETE', route('freights.destroy', $freight->id), ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Record has been deleted.'
            ]);

        $this->assertDatabaseMissing('freights', ['id' => $freight->id]);
    }
}
