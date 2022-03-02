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

        $workspaces = collect(Airtable::table('workspaces')->get())->map(function ($record) use ($amenities) {
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
        ]);
    }
}
