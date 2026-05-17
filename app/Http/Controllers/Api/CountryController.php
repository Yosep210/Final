<?php

namespace App\Http\Controllers\Api;

use App\Actions\Country\CreateCountryAction;
use App\Actions\Country\DeleteCountryAction;
use App\Actions\Country\GetCountryAction;
use App\Actions\Country\UpdateCountryAction;
use App\Data\CountryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $countries = GetCountryAction::run();

        return CountryResource::collection($countries)->response();
    }

    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = CreateCountryAction::run(CountryData::fromArray($request->validated()));

        return CountryResource::make($country)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Country $country): JsonResponse
    {
        return CountryResource::make($country)->response();
    }

    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $country = UpdateCountryAction::run($country, CountryData::fromArray($request->validated()));

        return CountryResource::make($country)->response();
    }

    public function destroy(Country $country): Response
    {
        DeleteCountryAction::run($country);

        return response()->noContent();
    }
}
