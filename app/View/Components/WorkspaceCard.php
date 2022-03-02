<?php

namespace App\View\Components;

use App\Models\Workspace;
use Illuminate\View\Component;

class WorkspaceCard extends Component
{
    public function __construct(public Workspace $workspace)
    {
    }

    public function render()
    {
        return view('components.workspace-card');
    }
}
