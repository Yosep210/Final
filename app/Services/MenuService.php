<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function get(string $group = 'Menu'): Collection
    {
        $menus = config("menu.{$group}", []);
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return self::filterMenu(is_array($menus) ? $menus : [], $user);
    }

    protected static function filterMenu(array $menus, Authenticatable $user): Collection
    {
        return collect($menus)
            ->filter(fn ($menu) => is_array($menu))
            ->map(function (array $menu) use ($user) {
                if (! empty($menu['sub']) && is_array($menu['sub'])) {
                    $menu['sub'] = self::filterMenu($menu['sub'], $user)->all();
                }

                return $menu;
            })
            ->filter(function (array $menu) use ($user) {
                if (isset($menu['sub']) && is_array($menu['sub'])) {
                    return count($menu['sub']) > 0;
                }

                return self::checkAccess($menu, $user);
            })
            ->values();
    }

    protected static function checkAccess(array $menu, Authenticatable $user): bool
    {
        if (empty($menu['permission']) && empty($menu['role'])) {
            return true;
        }

        if (isset($menu['permission']) && $user->can($menu['permission'])) {
            return true;
        }
        if (isset($menu['role']) && method_exists($user, 'hasRole') && $user->hasRole($menu['role'])) {
            return true;
        }

        return false;
    }
}
