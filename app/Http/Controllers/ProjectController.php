<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('projects.index');
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        // Временно редирект
        return redirect()->route('projects.index');
    }

    public function show(Project $project): View
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Временно редирект
        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        // Временно редирект
        return redirect()->route('projects.index');
    }
}
