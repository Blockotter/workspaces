<?php

namespace App\Console\Commands;

use App\Models\Amenity;
use App\Models\Package;
use App\Models\Workspace;
use DB;
use Illuminate\Console\Command;
use Str;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class CacheWorkspacesCommand extends Command
{
    protected $signature = 'workspaces:cache';

    protected $description = 'Cache all workspaces from Airtable';

    public function handle()
    {
        // Clear all databases
        Workspace::truncate();
        Amenity::truncate();
        Package::truncate();

        // Clear the pivot tables
        DB::table('amenity_workspace')->truncate();
        DB::table('package_workspace')->truncate();

        collect(Airtable::table('amenities')->get())->map(function ($item) {

            // Change all keys of $fields to snake_case
            $snake_cased_fields = [];
            foreach ($item['fields'] as $key => $value) {
                $snake_cased_fields[Str::snake($key)] = $value;
            }

            // Add the $item['id'] to the $snake_cased_fields
            $snake_cased_fields['airtable_id'] = $item['id'];

            Amenity::create($snake_cased_fields);
        });

        collect(Airtable::table('packages')->get())->map(function ($item) {
            // Change all keys of $fields to snake_case
            $snake_cased_fields = [];
            foreach ($item['fields'] as $key => $value) {
                $snake_cased_fields[Str::snake($key)] = $value;
            }

            // Add the $item['id'] to the $snake_cased_fields
            $snake_cased_fields['airtable_id'] = $item['id'];

            Package::create($snake_cased_fields);
        });

        collect(Airtable::table('workspaces')->get())->map(function ($item) {
            // Change all keys of $fields to snake_case
            $snake_cased_fields = [];
            foreach ($item['fields'] as $key => $value) {
                $snake_cased_fields[Str::snake($key)] = $value;
            }

            // Add the $item['id'] to the $snake_cased_fields
            $snake_cased_fields['airtable_id'] = $item['id'];

            // If an image has been set (which it should), add the first image to the workspace
            // Multiple images can be set. We only take the first one. Later, we could randomize this.
            // We take the large thumbnail image, because it is the smallest that doesn't look horrible
            if (isset($snake_cased_fields['image']) && isset($snake_cased_fields['image'][0]['thumbnails'])) {
                $snake_cased_fields['image_url'] = $snake_cased_fields['image'][0]['thumbnails']['large']['url'];
            }

            // Create the workspace
            $workspace = Workspace::create($snake_cased_fields);

            // Get the amenities from the $snake_cased_fields['amenities']
            if (isset($snake_cased_fields['amenities'])) {

                // Get the ids of the amenities
                $amenities = Amenity::whereIn('airtable_id', $snake_cased_fields['amenities'])->get();

                // Attach the amenities to the workspace (ManyToMany)
                $workspace->amenities()->attach($amenities);
            }

            // Get the packages from the $snake_cased_fields['packages']
            if (isset($snake_cased_fields['packages'])) {

                // Get the ids of the packages
                $packages = Package::whereIn('airtable_id', $snake_cased_fields['packages'])->get();

                // Attach the packages to the workspace (ManyToMany)
                $workspace->packages()->attach($packages);
            }
        });
    }
}
