<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'website',
        'is_recurring',
        'is_always_open',
        'cost',
        'minimum_hours',
        'opening_monday',
        'closing_monday',
        'opening_tuesday',
        'closing_tuesday',
        'opening_wednesday',
        'closing_wednesday',
        'opening_thursday',
        'closing_thursday',
        'opening_friday',
        'closing_friday',
        'opening_saturday',
        'closing_saturday',
        'opening_sunday',
        'closing_sunday',
        'airtable_id',
    ];

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }
}
