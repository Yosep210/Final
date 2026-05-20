<?php

namespace App\Http\Controllers\Api;

use App\Actions\Member\CreateMemberAction;
use App\Actions\Member\DeleteMemberAction;
use App\Actions\Member\GetMemberAction;
use App\Actions\Member\UpdateMemberAction;
use App\Data\MemberData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        $members = GetMemberAction::run();

        return MemberResource::collection($members)->response();
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = CreateMemberAction::run(MemberData::fromArray($request->validated()));

        return MemberResource::make($member)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Member $member): JsonResponse
    {
        return MemberResource::make($member)->response();
    }

    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $member = UpdateMemberAction::run($member, MemberData::fromArray($request->validated()));

        return MemberResource::make($member)->response();
    }

    public function destroy(Member $member): Response
    {
        DeleteMemberAction::run($member);

        return response()->noContent();
    }
}
