<?php

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

it('returns a paginated country list', function () {
    $user = User::factory()->create();
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
        ->actingAs($user)
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
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
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

it('shows a country', function () {
    $user = User::factory()->create();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->actingAs($user)
        ->getJson(route('countries.show', $country));

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $country->id)
        ->assertJsonPath('data.iso', 'ID')
        ->assertJsonPath('data.name', 'Indonesia');
});

it('updates a country', function () {
    $user = User::factory()->create();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->actingAs($user)
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

it('deletes a country', function () {
    $user = User::factory()->create();
    $country = Country::query()->create(countryPayload());

    $response = $this
        ->actingAs($user)
        ->deleteJson(route('countries.destroy', $country));

    $response->assertNoContent();

    $this->assertDatabaseMissing('countries', [
        'id' => $country->id,
    ]);
});
