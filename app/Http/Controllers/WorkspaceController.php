<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\AmenityOld;
use App\Models\Workspace;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class WorkspaceController extends Controller
{
    public function index()
    {
        // Set up Workspace query
        $workspaces = Workspace::with([
            'amenities',
            'packages'
        ]);

        // Check for place GET parameter
        if (request()->has('place')) {
            $workspaces->where('place', request('place'));
        } elseif (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'place=')) {
            // Get the place from the previous URL
            // This is to allow filters and search to work together
            $place = substr(request()->headers->get('referer'), strpos(request()->headers->get('referer'), 'place=') + 6);
            $workspaces->where('place', $place);
        }

        // Check for amenities GET parameter
        if (request()->has('amenities')) {
            foreach (request('amenities') as $amenityId) {
                $workspaces = $workspaces->whereHas('amenities', function ($query) use ($amenityId) {
                    $query->where('amenity_id', $amenityId);
                });
            }

            // Get the selected amenities and move them to a new collection
            $selected_amenities = Amenity::whereIn('id', request('amenities'))->get();

            // Get all amenities that are not selected
            $amenities = Amenity::whereNotIn('id', request('amenities'))->get();
        }

        // Get the workspaces
        $workspaces = $workspaces->get();

        return view('welcome', [
            'workspaces' => $workspaces,
            'amenities' => $amenities ?? Amenity::all(),
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
