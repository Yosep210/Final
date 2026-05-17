<?php

namespace App\Domain\Member\Actions;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMemberAction
{
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Member::query()
            ->latest('id')
            ->paginate($perPage);
    }
}
