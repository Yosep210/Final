<flux:dropdown position="bottom" align="start">
    <flux:radio.group class="flex">
        <flux:sidebar.profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
            data-test="sidebar-menu-button" />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:tooltip>
                <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle" class="cursor-pointer mt-1.5 ml-6"
                    onclick="return confirm('Are you sure you want to log out?')" data-test="logout-button">
                </flux:menu.item>
                <flux:tooltip.content>
                    {{ __('Log out') }}
                </flux:tooltip.content>
            </flux:tooltip>
        </form>
    </flux:radio.group>

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
