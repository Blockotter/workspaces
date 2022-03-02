<?php

namespace App\Models;

use Tapp\Airtable\Facades\AirtableFacade as Airtable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Str;

class Workspace extends Model
{
    public string $name;

    public string $place;
    public string $street_name;
    public string $building_number;
    public string $zipcode;
    public string $country;

    public string $website;
    public string $phone_number;
    public string $email;

    public string $image_url;
    public string $special_text;

    public float $internet_speed;

    public DateTime $created_at;
    public DateTime $updated_at;

    public Collection $workspace_packages;
    public Collection $workspace_amenities;

    public string $id;

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
        'workspace_packages',
        'workspace_amenities',
        'image_url',
        'special_text',
        'id',
    ];

    public function __construct(string $id, array $fields = [])
    {
        // Set the id of the workspace
        $this->setAttribute('id', $id);

        // Change all keys of $fields to snake_case
        $snake_cased_fields = [];
        foreach ($fields as $key => $value) {
            $snake_cased_fields[Str::snake($key)] = $value;
        }

        // If packages have been passed, we want to add them to the workspace
        // as a collection of packages, for easier access
        if (isset($snake_cased_fields['packages'])) {

            // Create a collection of packages
            $workspace_packages = collect();

            // Loop through each package
            foreach ($snake_cased_fields['packages'] as $package) {

                // Find the package on Airtable
                // We can loop over this this way, because we know that a package
                // will never be used twice. Therefore, we are not doing duplicate checks
                $package_object = Airtable::table('packages')->find($package);

                // Store the fields in a distinct array, so we can add the workspace id to it later
                $fields = $package_object['fields'];

                // Append the workspace id to the fields
                if (isset($fields['Workspace']) && !empty($fields['Workspace'])) {
                    $fields['workspace_id'] = $fields['Workspace'][0];
                }

                // Create a new package object and add it to the collection
                $workspace_packages->push(new Package($package_object['id'], $fields));
            }

            // Add the collection to the workspace
            $this->setAttribute('workspace_packages', $workspace_packages);
        }

        // If an image has been set (which it should), add the first image to the workspace
        // Multiple images can be set. We only take the first one. Later, we could randomize this.
        // We take the large thumbnail image, because it is the smallest that doesn't look horrible
        if (isset($snake_cased_fields['image']) && isset($snake_cased_fields['image'][0]['thumbnails'])) {
            $this->setAttribute('image_url', $snake_cased_fields['image'][0]['thumbnails']['large']['url']);
        }

        // Set the attributes of the workspace
        parent::__construct($snake_cased_fields);
    }

    public function getName(): string
    {
        // Get the name of the workspace
        $name = $this->getAttribute('name');

        // If the name ends at 'Coworking Space', remove it
        if (str_ends_with($name, 'Coworking Space')) {
            $name = substr($name, 0, -15);
        }

        // Return the name of the workspace
        return $name;
    }

    public function getAmenityEmojis(): string|null
    {
        // Get the amenities
        $amenities = $this->getAttribute('workspace_amenities');

        // If there are no amenities, return null
        if (! $amenities) {
            return null;
        }

        // Take the first 3 amenities
        $amenities = $amenities->take(3);

        // Create a string of emojis
        $emojis = '';
        foreach ($amenities as $amenity) {
            $emojis .= ' ' . $amenity->getAttribute('emoji');
        }

        // Return the emojis
        return $emojis;
    }

    public function getPrice()
    {
        // If there are no packages, return null
        if (! $this->getAttribute('workspace_packages')) {
            return null;
        }

        // Go over the packages and find the cheapest one
        $cheapest_package = null;
        foreach ($this->getAttribute('workspace_packages') as $package) {
            if ($cheapest_package == null || $package->getAttribute('cost') < $cheapest_package->getAttribute('cost')) {
                $cheapest_package = $package;
            }
        }

        // Return the cheapest package
        return $cheapest_package;
    }

    public function getSpecialText(): string|null
    {
        // Get the special text
        $special_text = $this->getAttribute('special_text');

        // If there is no special text, return null
        if (! $special_text) {
            return null;
        }

        // Return the special text
        return $special_text;
    }
}
