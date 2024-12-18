<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            return redirect()->route('projects')->with('warning', 'You are not authorized to view pages for this project.');
        }

        $pages = $project->pages->map(function ($page) {
            $testCases = $page->testCases;
            $page->pending_count = $testCases->where('test_status', 0)->count();
            $page->pass_count = $testCases->where('test_status', 1)->count();
            $page->fail_count = $testCases->where('test_status', 2)->count();
            $page->total_tests = $testCases->count();

            return $page;
        });

        return view('dashboard.pages.index', compact('project', 'pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(Project $project)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('warning', 'You are not authorized to create a page for this project.');
        }

        return view('dashboard.pages.create', compact('project'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request, Project $project)
    {
        // Check if the user is authorized to access the project
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('warning', 'You are not authorized to store a page for this project.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'requirement' => 'required|string',
        ]);

        $project->pages()->create([
            'name' => $validatedData['name'],
            'requirement' => $validatedData['requirement'],
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('page.index', $project)->with('success', 'Page created successfully!');
    }

    public function show(Project $project, Page $page)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('warning', 'You are not authorized to view this page for this project.');
        }

        return view('dashboard.pages.show', compact('project', 'page'));
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
            'requirement' => 'required',
        ]);

        $page->update([
            'name' => $request->name,
            'requirement' => $request->requirement,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('page.index', $project)->with('success', 'Page updated successfully!');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Request $request, Project $project, Page $page)
    {
        $this->authorize('delete-page', $page);

        $request->validate([
            'page_name_confirmation' => ['required', 'string', function ($attribute, $value, $fail) use ($page) {
                if ($value !== $page->name) {
                    $fail('The confirmation text does not match the page name.');
                }
            }],
        ]);

        $deletedTestCasesCount = $page->testCases->count();

        DB::transaction(function () use ($page) {
            $page->testCases()->delete();
            $page->delete();
        });

        return redirect()->route('page.index', $project)->with('success', 'Page deleted successfully!');
    }
}
