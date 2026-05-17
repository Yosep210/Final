<?php

namespace App\Http\Controllers\Api;

use App\Actions\Province\CreateProvinceAction;
use App\Actions\Province\DeleteProvinceAction;
use App\Actions\Province\GetProvinceAction;
use App\Actions\Province\UpdateProvinceAction;
use App\Data\ProvinceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Province\StoreProvinceRequest;
use App\Http\Requests\Province\UpdateProvinceRequest;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProvinceController extends Controller
{
    public function index(): JsonResponse
    {
        $provinces = GetProvinceAction::run();

        return ProvinceResource::collection($provinces)->response();
    }

    /**
     * Store a newly created province.
     */
    public function store(StoreProvinceRequest $request): JsonResponse
    {
        $province = CreateProvinceAction::run(ProvinceData::fromArray($request->validated()));

        return ProvinceResource::make($province)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified province.
     */
    public function show(Province $province): JsonResponse
    {
        return ProvinceResource::make($province)
            ->response();
    }

    /**
     * Update the specified province.
     */
    public function update(UpdateProvinceRequest $request, Province $province): JsonResponse
    {
        $province = UpdateProvinceAction::run($province, ProvinceData::fromArray($request->validated()));

        return ProvinceResource::make($province)
            ->response();
    }

    /**
     * Remove the specified province.
     */
    public function destroy(Province $province): JsonResponse
    {
        DeleteProvinceAction::run($province);

        return response()->noContent();
    }
}
