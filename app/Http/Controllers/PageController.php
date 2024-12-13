<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(['role:super-admin'])->only(['edit', 'update', 'destroy']);
    }

    public function index(Project $project)
    {
        $pages = $project->pages;
        if (auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user())) {
            return view('dashboard.pages.index', compact('project', 'pages'));
        }

        return redirect()->route('projects')->with('error', 'You are not authorized to create a page for this project.');
    }

    public function create(Project $project)
    {
        if (auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user())) {
            return view('dashboard.pages.create', compact('project'));
        }

        return redirect()->route('projects')->with('error', 'You are not authorized to create a page for this project.');
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->pages()->create([
            'name' => $request->name,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('page.index', $project)->with('success', 'Page created successfully!');
    }

    public function show(Project $project, Page $page)
    {
        if (auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user())) {
            return view('dashboard.pages.show', compact('project', 'page'));
        }

        return redirect()->route('page.index', $project)->with('error', 'You are not authorized to view this page.');
    }

    public function edit(Project $project, Page $page)
    {
        $this->authorize('edit-page', $page);

        return view('dashboard.pages.edit', compact('project', 'page'));
    }

    public function update(Request $request, Project $project, Page $page)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $page->update([
            'name' => $request->name,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('page.index', $project)->with('success', 'Page updated successfully!');
    }

    public function destroy(Project $project, Page $page)
    {
        $this->authorize('delete-page', $page);

        $page->delete();

        return redirect()->route('page.index', $project)->with('success', 'Page deleted successfully!');
    }
}
