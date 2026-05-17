<?php

namespace App\Http\Controllers;

use App\Domain\Member\Actions\CreateMemberAction;
use App\Domain\Member\Actions\DeleteMemberAction;
use App\Domain\Member\Actions\GetMemberAction;
use App\Domain\Member\Actions\UpdateMemberAction;
use App\Domain\Member\Data\MemberData;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    public function index(GetMemberAction $getMemberAction): JsonResponse
    {
        $members = $getMemberAction->execute();

        return MemberResource::collection($members)->response();
    }

    public function store(StoreMemberRequest $request, CreateMemberAction $createMemberAction): JsonResponse
    {
        $memberData = MemberData::fromArray($request->validated());
        $member = $createMemberAction->execute($memberData);

        return MemberResource::make($member)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Member $member): JsonResponse
    {
        return MemberResource::make($member)->response();
    }

    public function update(UpdateMemberRequest $request, Member $member, UpdateMemberAction $updateMemberAction): JsonResponse
    {
        $memberData = MemberData::fromArray($request->validated());
        $updatedMember = $updateMemberAction->execute($member, $memberData);

        return MemberResource::make($updatedMember)->response();
    }

    public function destroy(Member $member, DeleteMemberAction $deleteMemberAction): Response
    {
        $deleteMemberAction->execute($member);

        return response()->noContent();
    }
}
