<?php

namespace App\Http\Controllers\Api;

use App\Actions\Member\CreateMemberAction;
use App\Actions\Member\DeleteMemberAction;
use App\Actions\Member\GetMemberAction;
use App\Actions\Member\UpdateMemberAction;
use App\Data\MemberData;
use App\Events\MemberPromoted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Member::class);

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
        $this->authorize('view', $member);

        return MemberResource::make($member->load('profile', 'network'))->response();
    }

    public function update(UpdateMemberRequest $request, Member $member): JsonResponse
    {
        $member = UpdateMemberAction::run($member, MemberData::fromArray($request->validated()));

        return MemberResource::make($member)->response();
    }

    public function destroy(Member $member): Response
    {
        $this->authorize('delete', $member);

        DeleteMemberAction::run($member);

        return response()->noContent();
    }

    public function network(Member $member): JsonResponse
    {
        $this->authorize('view', $member);

        $network = $member->network ?? MemberNetwork::where('member_id', $member->id)->first();

        return response()->json($network);
    }

    public function promote(Request $request, Member $member): JsonResponse
    {
        $this->authorize('update', $member);

        $payload = $request->all();

        event(new MemberPromoted($member, $payload));

        return response()->json(['status' => 'ok']);
    }
}
