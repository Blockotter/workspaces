<?php

namespace App\Http\Controllers;
use App\Models\Workspace;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class WorkspaceController extends Controller
{
    public function index()
    {
        $records = Airtable::table('workspaces')->get();

        $workspaces = collect($records)->map(function ($record) {
            return new Workspace($record['fields']);
        });

        return view('welcome', [
            'workspaces' => $workspaces,
        ]);
    }
}
