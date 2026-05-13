<?php

namespace App\Http\Controllers;

use App\Domain\Country\Actions\CreateCountryAction;
use App\Domain\Country\Actions\DeleteCountryAction;
use App\Domain\Country\Actions\GetCountryAction;
use App\Domain\Country\Actions\UpdateCountryAction;
use App\Domain\Country\Data\CountryData;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    /**
     * Display a paginated listing of countries.
     */
    public function index(GetCountryAction $getCountryAction): JsonResponse
    {
        $countries = $getCountryAction->execute();

        return CountryResource::collection($countries)->response();
    }

    /**
     * Store a newly created country.
     */
    public function store(StoreCountryRequest $request, CreateCountryAction $createCountryAction): JsonResponse
    {
        $countryData = CountryData::fromArray($request->validated());
        $country = $createCountryAction->execute($countryData);

        return CountryResource::make($country)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country): JsonResponse
    {
        return CountryResource::make($country)->response();
    }

    /**
     * Update the specified country.
     */
    public function update(UpdateCountryRequest $request, Country $country, UpdateCountryAction $updateCountryAction): JsonResponse
    {
        $countryData = CountryData::fromArray($request->validated());
        $updatedCountry = $updateCountryAction->execute($country, $countryData);

        return CountryResource::make($updatedCountry)->response();
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country, DeleteCountryAction $deleteCountryAction): Response
    {
        $deleteCountryAction->execute($country);

        return response()->noContent();
    }
}
