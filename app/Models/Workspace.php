<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'place',
        'street_name',
        'building_number',
        'zipcode',
        'country',
        'created_at',
        'updated_at',
        'website',
        'phone_number',
        'email',
        'internet_speed',
        'image_url',
        'special_text',
        'airtable_id',
    ];

    public function amenities(): BelongsToMany
    {
        // Get the amenities of the workspace
        return $this->belongsToMany(Amenity::class);
    }

    public function packages(): BelongsToMany
    {
        // Get the packages of the workspace
        return $this->belongsToMany(Package::class);
    }

    public function getName(): string
    {
        // Get the name of the workspace
        $name = $this->name;

        // If the name ends at 'Coworking Space', remove it
        if (str_ends_with($name, 'Coworking Space')) {
            $name = substr($name, 0, -15);
        }

        // Return the name of the workspace
        return $name;
    }

    public function getPrice(): Package|null
    {
        // If there is no package, return null
        if (! $this->packages->count()) {
            return null;
        }

        // Find the package with the lowest cost
        $package = $this->packages->sortBy('cost')->first();

        // Return the package
        return $package;
    }

    public function getAmenityEmojis(): string
    {
        // Get the amenities of the workspace
        $amenities = $this->amenities;

        // If there are no amenities, return an empty string
        if (! $amenities->count()) {
            return '';
        }

        // Get the emojis of the amenities
        $emojis = $amenities->pluck('emoji')->implode('');

        // Return the emojis of the amenities
        return $emojis;
    }
}
