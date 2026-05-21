<?php

namespace App\Livewire\Member;

use App\Actions\Member\CreateMemberAction;
use App\Actions\Member\UpdateMemberAction;
use App\Data\MemberData;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Models\Member;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Member')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $editingMemberId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->authorize('create', Member::class);

        $this->editingMemberId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('member:edit')]
    public function edit(int $memberId): void
    {
        $member = Member::query()->findOrFail($memberId);
        $this->authorize('Update', $member);

        $this->editingMemberId = $member->id;
        $this->form = [
            'name' => $member->name,
            'username' => $member->username,
            'email' => $member->email,
            'password' => '',
            'password_confirmation' => '',
            'status' => $member->status,
            'referral_code' => $member->referral_code,
            'email_verified_at' => optional($member->email_verified_at)->format('Y-m-d\TH:i'),
            'last_login_at' => optional($member->last_login_at)->format('Y-m-d\TH:i'),
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $member = $this->editingMemberId
            ? Member::query()->findOrFail($this->editingMemberId)
            : null;

        if ($member) {
            $this->authorize('update', $member);
        } else {
            $this->authorize('create', Member::class);
        }

        $validated = $this->validate($this->rules($member), [], $this->attributes());
        $memberData = MemberData::fromArray($validated['form']);

        if ($member) {
            UpdateMemberAction::run($member, $memberData);
            Flux::toast(variant: 'success', text: 'Member updated successfully.');
        } else {
            CreateMemberAction::run($memberData);
            Flux::toast(variant: 'success', text: 'Member created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-memberTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingMemberId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.member.index')
            ->layout('layouts.app', ['title' => __('Member')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Member $member = null): array
    {
        $rules = [
            'form' => ['array'],
            'form.*' => ['nullable'],
        ];

        foreach (StoreMemberRequest::memberRules($member) as $key => $ruleSet) {
            $rules["form.$key"] = $ruleSet;
        }

        $rules['form.password'] = $member
            ? ['nullable', 'string', Password::default(), 'confirmed']
            : ['required', 'string', Password::default(), 'confirmed'];
        $rules['form.password_confirmation'] = $member
            ? ['nullable', 'string']
            : ['required', 'string'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        $attributes = [];

        foreach (StoreMemberRequest::attributeLabels() as $key => $value) {
            $attributes["form.$key"] = $value;
        }

        $attributes['form.password'] = 'password';
        $attributes['form.password_confirmation'] = 'password confirmation';

        return $attributes;
    }

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'status' => 'active',
            'referral_code' => '',
            'email_verified_at' => '',
            'last_login_at' => '',
        ];
    }
}
