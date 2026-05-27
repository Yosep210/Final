<?php

use App\Models\Country;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function countryPayload(array $overrides = []): array
{
    return array_merge([
        'iso' => 'ID',
        'name' => 'Indonesia',
        'nice_name' => 'Indonesia',
        'iso3' => 'IDN',
        'numcode' => 360,
        'phonecode' => 62,
        'status' => true,
    ], $overrides);
}

function actingAsAdminForCountryApi(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    Sanctum::actingAs($admin);

    return $admin;
}

it('returns a paginated country list', function () {
    actingAsAdminForCountryApi();
    Country::query()->create(countryPayload());
    Country::query()->create(countryPayload([
        'iso' => 'MY',
        'name' => 'Malaysia',
        'nice_name' => 'Malaysia',
        'iso3' => 'MYS',
        'numcode' => 458,
        'phonecode' => 60,
    ]));

    $response = $this
        ->getJson(route('countries.index'));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'iso', 'name', 'nice_name', 'iso3', 'numcode', 'phonecode', 'status', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('stores a country', function () {
    actingAsAdminForCountryApi();

    $response = $this
        ->postJson(route('countries.store'), countryPayload());

    $response
        ->assertCreated()
        ->assertJsonPath('data.iso', 'ID');

    $this->assertDatabaseHas('countries', [
        'iso' => 'ID',
        'name' => 'Indonesia',
        'nice_name' => 'Indonesia',
    ]);
});

it('fails to store a country with duplicate iso', function () {
    actingAsAdminForCountryApi();
    Country::query()->create(countryPayload());

    $response = $this
        ->postJson(route('countries.store'), countryPayload([
            'name' => 'Indonesia Duplicate',
            'nice_name' => 'Indonesia Duplicate',
            'iso3' => 'IDX',
            'numcode' => 361,
            'phonecode' => 620,
        ]));

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['iso']);
});

it('shows a country', function () {
    actingAsAdminForCountryApi();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->getJson(route('countries.show', $country));

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $country->id)
        ->assertJsonPath('data.iso', 'ID')
        ->assertJsonPath('data.name', 'Indonesia');
});

it('updates a country', function () {
    actingAsAdminForCountryApi();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->putJson(route('countries.update', $country), countryPayload([
            'name' => 'Republik Indonesia',
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('data.name', 'Republik Indonesia');

    $this->assertDatabaseHas('countries', [
        'id' => $country->id,
        'name' => 'Republik Indonesia',
    ]);
});

it('fails to update a country with duplicate iso', function () {
    actingAsAdminForCountryApi();
    Country::query()->create(countryPayload());
    $country = Country::query()->create(countryPayload([
        'iso' => 'MY',
        'name' => 'Malaysia',
        'nice_name' => 'Malaysia',
        'iso3' => 'MYS',
        'numcode' => 458,
        'phonecode' => 60,
    ]));

    $response = $this
        ->putJson(route('countries.update', $country), countryPayload([
            'iso' => 'ID',
            'name' => 'Malaysia',
            'nice_name' => 'Malaysia',
            'iso3' => 'MYS',
            'numcode' => 458,
            'phonecode' => 60,
        ]));

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['iso']);
});

it('deletes a country', function () {
    actingAsAdminForCountryApi();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->deleteJson(route('countries.destroy', $country));

    $response->assertNoContent();

    $this->assertDatabaseMissing('countries', [
        'id' => $country->id,
    ]);
});

it('blocks deleting a country that is already referenced by province data', function () {
    actingAsAdminForCountryApi();
    $country = Country::query()->create(countryPayload());

    DB::table('provinces')->insert([
        'country_id' => $country->id,
        'name' => 'Jawa Barat',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->deleteJson(route('countries.destroy', $country));

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['country']);

    $this->assertDatabaseHas('countries', [
        'id' => $country->id,
    ]);
});

it('rejects guests from listing countries', function () {
    $this->getJson(route('countries.index'))
        ->assertUnauthorized();
});

it('forbids non-admin members from listing countries', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    $this->getJson(route('countries.index'))
        ->assertForbidden();
});
