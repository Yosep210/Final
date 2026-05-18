<?php

namespace App\Http\Controllers\Api;

use App\Actions\District\CreateDistrictAction;
use App\Actions\District\DeleteDistrictAction;
use App\Actions\District\GetDistrictAction;
use App\Actions\District\UpdateDistrictAction;
use App\Data\DistrictData;
use App\Http\Controllers\Controller;
use App\Http\Requests\District\StoreDistrictRequest;
use App\Http\Requests\District\UpdateDistrictRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DistrictController extends Controller
{
    public function index(): JsonResponse
    {
        $districts = GetDistrictAction::run();

        return DistrictResource::collection($districts)->response();
    }

    public function store(StoreDistrictRequest $request): JsonResponse
    {
        $district = CreateDistrictAction::run(DistrictData::fromArray($request->validated()));

        return DistrictResource::make($district)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(District $district): JsonResponse
    {
        return DistrictResource::make($district)->response();
    }

    public function update(UpdateDistrictRequest $request, District $district): JsonResponse
    {
        $district = UpdateDistrictAction::run(DistrictData::fromArray($request->validated()), $district);

        return DistrictResource::make($district)->response();
    }

    public function destroy(District $district): Response
    {
        DeleteDistrictAction::run(DistrictData::fromModel($district));

        return response()->noContent();
    }
}
