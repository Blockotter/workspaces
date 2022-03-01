<?php

namespace App\Http\Controllers;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;

class WorkspaceController extends Controller
{
    public function index()
    {
        $records = Airtable::table('workspaces')->get();

        return view('welcome', [
            'records' => $records,
        ]);
    }
}
