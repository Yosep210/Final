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
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $members = Member::all();

        return response()->json([
            'data' => MemberResource::collection($members),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $memberData = MemberData::fromArray($request->validated());
        $member = (new CreateMemberAction)->execute($memberData);

        return response()->json([
            'data' => MemberResource::make($member),
            'message' => 'Member created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): JsonResponse
    {
        $member = (new GetMemberAction)->execute($member->id);

        return response()->json([
            'data' => MemberResource::make($member),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $memberData = MemberData::fromArray($request->validated());
        $updated = (new UpdateMemberAction)->execute($member->id, $memberData);

        return response()->json([
            'data' => MemberResource::make($updated),
            'message' => 'Member updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): Response
    {
        (new DeleteMemberAction)->execute($member->id);

        return response()->noContent();
    }
}
