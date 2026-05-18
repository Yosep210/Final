<?php

namespace App\Http\Controllers\Api;

use App\Actions\City\CreateCityAction;
use App\Actions\City\DeleteCityAction;
use App\Actions\City\GetCityAction;
use App\Actions\City\UpdateCityAction;
use App\Data\CityData;
use App\Http\Controllers\Controller;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = GetCityAction::run();

        return CityResource::collection($cities)->response();
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = CreateCityAction::run(CityData::fromArray($request->validated()));

        return CityResource::make($city)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(City $city): JsonResponse
    {
        return CityResource::make($city)->response();
    }

    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $city = UpdateCityAction::run($city, CityData::fromArray($request->validated()));

        return CityResource::make($city)->response();
    }

    public function destroy(City $city): Response
    {
        DeleteCityAction::run($city);

        return response()->noContent();
    }
}
