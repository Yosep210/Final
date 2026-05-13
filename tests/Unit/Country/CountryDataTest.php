<?php

use App\Domain\Country\Data\CountryData;

it('normalizes country data from array input', function () {
    $data = CountryData::fromArray([
        'iso' => ' id ',
        'name' => ' Indonesia ',
        'nice_name' => ' Indonesia ',
        'iso3' => ' idn ',
        'numcode' => '',
        'phonecode' => '62',
        'status' => 1,
    ]);

    expect($data->iso)->toBe('ID')
        ->and($data->name)->toBe('Indonesia')
        ->and($data->niceName)->toBe('Indonesia')
        ->and($data->iso3)->toBe('IDN')
        ->and($data->numcode)->toBeNull()
        ->and($data->phonecode)->toBe(62)
        ->and($data->status)->toBeTrue();
});
