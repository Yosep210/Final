<?php

use App\Http\Requests\Member\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Validation\Rules\Unique;

it('builds create member rules without ignored model', function () {
    $rules = StoreMemberRequest::memberRules();
    $uniqueRule = collect($rules['username'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:members,username');
});

it('builds update member rules with ignored model', function () {
    $member = new Member;
    $member->id = 7;

    $rules = StoreMemberRequest::memberRules($member);
    $uniqueRule = collect($rules['username'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:members,username');
});
