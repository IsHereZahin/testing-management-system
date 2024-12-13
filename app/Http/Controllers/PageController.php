<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Project;
use App\Models\TestCase;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Check if the user is authorized to access the project.
     */
    protected function authorizeProjectAccess(Project $project)
    {
        return auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user());
    }

    /**
     * Display a listing of pages for a project.
     */
    public function index(Project $project)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to view pages for this project.');
        }

        $pages = $project->pages;
        return view('dashboard.pages.index', compact('project', 'pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(Project $project)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to create a page for this project.');
        }

        return view('dashboard.pages.create', compact('project'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request, Project $project)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to store a page for this project.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->pages()->create([
            'name' => $request->name,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('page.index', $project)->with('success', 'Page created successfully!');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Project $project, Page $page)
    {
        $this->authorize('edit-page', $page);

        return view('dashboard.pages.edit', compact('project', 'page'));
    }

    /**
     * Update the specified page in storage.
     */
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

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Project $project, Page $page)
    {
        $this->authorize('delete-page', $page);

        $page->delete();

        return redirect()->route('page.index', $project)->with('success', 'Page deleted successfully!');
    }
}
