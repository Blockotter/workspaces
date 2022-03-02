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

        // Check for amenities GET parameter
        if (request()->has('amenities')) {
            foreach (request('amenities') as $amenityId) {
                $workspaces = $workspaces->filter(function ($workspace) use ($amenityId) {
                    if ($workspace->getAttribute('workspace_amenities')) {
                        foreach ($workspace->getAttribute('workspace_amenities') as $amenity) {
                            if ($amenity->getAttributes()['id'] === $amenityId) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
            }
        }

        return view('welcome', [
            'workspaces' => $workspaces,
            'amenities' => $amenities,
        ]);
    }

    public function addFilter($key, $value)
    {
        // Get the current URL
        $url = request()->headers->get('referer');

        // Add ['amenities[]' => $value] to the URL
        // First, check if the URL already has GET parameters
        if (str_contains($url, '?')) {
            // If it does, add the new parameter to the end of the URL
            $url .= '&' . $key . '=' . $value;
        } else {
            // If it doesn't, add the new parameter to the end of the URL
            $url .= '?' . $key . '=' . $value;
        }

        // Redirect to the new URL
        return redirect($url);
    }

    public function removeFilter($key, $value)
    {
        // Get the current URL
        $url = request()->headers->get('referer');

        // Remove ['amenities[]' => $value] from the URL
        if (str_contains($url, '?')) {
            // If it does, remove the parameter from the end of the URL
            $url = str_replace('&' . $key . '=' . $value, '', $url);
        }

        // Redirect to the new URL
        return redirect($url);
    }
}
