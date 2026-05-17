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
    public function index(): JsonResponse
    {
        $countries = GetCountryAction::run();

        return CountryResource::collection($countries)->response();
    }

    /**
     * Store a newly created country.
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $countryData = CountryData::fromArray($request->validated());
        $country = CreateCountryAction::run($countryData);

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
    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $countryData = CountryData::fromArray($request->validated());
        $updatedCountry = UpdateCountryAction::run($country, $countryData);

        return CountryResource::make($updatedCountry)->response();
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country): Response
    {
        DeleteCountryAction::run($country);

        return response()->noContent();
    }
}
