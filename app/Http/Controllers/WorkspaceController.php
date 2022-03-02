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

            // Get the selected amenities and move them to a new collection
            // Remove the selected amenities from the amenities collection
            $selected_amenities = collect(request('amenities'))->map(function ($amenityId) use ($amenities) {
                return $amenities->filter(function ($amenity) use ($amenityId) {
                    return $amenity->getAttributes()['id'] === $amenityId;
                })->first();
            });

            $amenities = $amenities->filter(function ($amenity) use ($selected_amenities) {
                return !$selected_amenities->contains($amenity);
            });
        }

        return view('welcome', [
            'workspaces' => $workspaces,
            'amenities' => $amenities,
            'selected_amenities' => $selected_amenities ?? null,
            'place' => request('place') ?? null,
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

        // Replace the parameter with an empty string
        // It can use both & and ?
        $url = str_replace('&' . $key . '=' . $value, '', $url);
        $url = str_replace('?' . $key . '=' . $value, '', $url);

        // Now, the URL could start with & instead of ?
        // If it does, replace the & with ?
        // I am terribly sorry for this
        if (str_contains($url, '/&')) {
            $url = str_replace('/&', '/?', $url);
        }

        // Redirect to the new URL
        return redirect($url);
    }

    public function search()
    {
        // Get the current URL
        $url = request()->headers->get('referer');

        // Get the place from the GET parameter
        $place = request('place');

        // Check if the URL already has a place parameter
        if (str_contains($url, 'place')) {
            // If it does, replace the place parameter with the new one
            $url = str_replace('place=' . request()->query('place'), 'place=' . $place, $url);
        } else {
            // If it doesn't, check if the URL already has GET parameters
            if (str_contains($url, '?')) {
                // If it does, add the new parameter to the end of the URL
                $url .= '&place=' . $place;
            } else {
                // If it doesn't, add the new parameter to the end of the URL
                $url .= '?place=' . $place;
            }
        }

        // Redirect to the new URL
        return redirect($url);
    }
}
