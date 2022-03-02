<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Workspace;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class WorkspaceController extends Controller
{
    public function index()
    {
        $amenities = collect(Airtable::table('amenities')->get())->map(function ($item) {
            return new Amenity($item['id'], $item['fields']);
        });

        $airtable_query = Airtable::table('workspaces');

        // Check for place GET parameter
        if (request()->has('place')) {
            $airtable_query->where('Place', request('place'));
        } elseif (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'place=')) {
            // Get the place from the previous URL
            // This is to allow filters and search to work together
            $place = substr(request()->headers->get('referer'), strpos(request()->headers->get('referer'), 'place=') + 6);
            $airtable_query->where('Place', $place);
        }

        $workspaces = collect($airtable_query->get())->map(function ($record) use ($amenities) {
            $workspace = new Workspace($record['id'], $record['fields']);
            if (isset($record['fields']['Amenities'])) {
                $workspace->setAttribute('workspace_amenities', $record['fields']['Amenities']
                    ? collect($record['fields']['Amenities'])->map(function ($amenityId) use ($amenities) {
                        return $amenities->filter(function ($amenity) use ($amenityId) {
                            return $amenity->getAttributes()['id'] === $amenityId;
                        })->first();
                    })
                    : collect());
            }
            return $workspace;
        });

        return view('welcome', [
            'workspaces' => $workspaces,
            'amenities' => $amenities,
        ]);
    }
}
