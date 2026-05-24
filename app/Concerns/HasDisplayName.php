<?php

namespace App\Concerns;

trait HasDisplayName
{
    /**
     * Return the display label for this model.
     */
    public function getDisplayNameAttribute(): string
    {
        return (string) ($this->nice_name ?? $this->name ?? $this->title ?? '');
    }

    /**
     * Alias for display name.
     */
    public function getLabelAttribute(): string
    {
        return $this->display_name;
    }
}
