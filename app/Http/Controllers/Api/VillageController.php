<?php

namespace App\Http\Controllers\Api;

use App\Actions\Village\CreateVillageAction;
use App\Actions\Village\DeleteVillageAction;
use App\Actions\Village\GetVillageAction;
use App\Actions\Village\UpdateVillageAction;
use App\Data\VillageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Village\StoreVillageRequest;
use App\Http\Requests\Village\UpdateVillageRequest;
use App\Http\Resources\VillageResource;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VillageController extends Controller
{
    public function index(): JsonResponse
    {
        $villages = GetVillageAction::run();

        return VillageResource::collection($villages)->response();
    }

    public function store(StoreVillageRequest $request): JsonResponse
    {
        $village = CreateVillageAction::run(VillageData::fromArray($request->validated()));

        return VillageResource::make($village)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Village $village): JsonResponse
    {
        return VillageResource::make($village)->response();
    }

    public function update(UpdateVillageRequest $request, Village $village): JsonResponse
    {
        $village = UpdateVillageAction::run($village, VillageData::fromArray($request->validated()));

        return VillageResource::make($village)->response();
    }

    public function destroy(Village $village): Response
    {
        DeleteVillageAction::run($village);

        return response()->noContent();
    }
}
