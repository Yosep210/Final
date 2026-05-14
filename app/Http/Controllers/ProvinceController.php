<?php

namespace App\Http\Controllers;

use App\Domain\Province\Actions\CreateProvinceAction;
use App\Domain\Province\Actions\DeleteProvinceAction;
use App\Domain\Province\Actions\GetProvinceAction;
use App\Domain\Province\Actions\UpdateProvinceAction;
use App\Domain\Province\Data\ProvinceData;
use App\Http\Requests\Province\StoreProvinceRequest;
use App\Http\Requests\Province\UpdateProvinceRequest;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProvinceController extends Controller
{
    /**
     * Display a paginated listing of provinces.
     */
    public function index(GetProvinceAction $getProvinceAction): JsonResponse
    {
        $provinces = $getProvinceAction->execute();

        return ProvinceResource::collection($provinces)->response();
    }

    /**
     * Store a newly created province.
     */
    public function store(StoreProvinceRequest $request, CreateProvinceAction $createProvinceAction): JsonResponse
    {
        $provinceData = ProvinceData::fromArray($request->validated());
        $province = $createProvinceAction->execute($provinceData);

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
    public function update(UpdateProvinceRequest $request, Province $province, UpdateProvinceAction $updateProvinceAction): JsonResponse
    {
        $provinceData = ProvinceData::fromArray($request->validated());
        $updatedProvince = $updateProvinceAction->execute($province, $provinceData);

        return ProvinceResource::make($updatedProvince)
            ->response();
    }

    /**
     * Remove the specified province.
     */
    public function destroy(DeleteProvinceAction $deleteProvinceAction, Province $province): JsonResponse
    {
        $deleteProvinceAction->execute($province);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
