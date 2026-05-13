<?php

use App\Domain\Country\Support\CountryValidation;
use App\Models\Country;
use Illuminate\Validation\Rules\Unique;

it('builds create rules without ignored model', function () {
    $rules = CountryValidation::rules();
    $uniqueRule = collect($rules['iso'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:countries,iso');
});

it('builds update rules with ignored model', function () {
    $country = new Country;
    $country->id = 99;

    $rules = CountryValidation::rules($country);
    $uniqueRule = collect($rules['iso'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:countries,iso');
});
