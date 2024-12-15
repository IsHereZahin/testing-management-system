<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use App\Models\Project;
use App\Models\Page;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    /**
     * Check if the user is authorized to access the project.
     */
    protected function authorizeProjectAccess(Project $project)
    {
        return auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user());
    }

    /**
     * Display a listing of the test cases for a page.
     */
    public function index(Project $project, Page $page)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to view test cases for this project.');
        }

        $testCases = TestCase::where('page_id', $page->id)->get();
        return view('dashboard.test_cases.index', compact('testCases', 'project', 'page'));
    }

    /**
     * Show the form for creating a new test case.
     */
    public function create(Project $project, Page $page)
    {
        // Check authorization for the project
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to create a test case for this project.');
        }

        $sections = TestCase::where('page_id', $page->id)->distinct()->pluck('section');

        return view('dashboard.test_cases.create', compact('project', 'page', 'sections'));
    }

    /**
     * Store a newly created test case in storage.
     */
    public function store(Request $request, Project $project, Page $page)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to store a test case for this project.');
        }

        $validated = $request->validate([
            'test_case_id' => 'required|string|max:255',
            'test_title' => 'required|string|max:255',
            'description' => 'required',
            'test_status' => 'nullable',
            'comments' => 'nullable|string',
        ]);

        // Handle section input
        $section = $request->section;
        if (!$section && $request->new_section) {
            $section = $request->new_section;
        }
        if (!$section) {
            return redirect()->back()->withErrors(['section' => 'Please select or create a new section.']);
        }

        TestCase::create([
            'section' => $section,
            'test_case_id' => $validated['test_case_id'],
            'test_title' => $validated['test_title'],
            'description' => $validated['description'],
            // 'test_status' => $validated['test_status'],
            'comments' => $validated['comments'] ?? null,
            'tested_by' => auth()->id(),
            'page_id' => $page->id,
        ]);

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case created successfully.');
    }

    /**
     * Display the specified test case.
     */
    public function show(Project $project, Page $page, TestCase $test)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to view this test case.');
        }

        return view('dashboard.test_cases.show', compact('test', 'project', 'page'));
    }

    /**
     * Show the form for editing the specified test case.
     */
    public function edit(Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to view this test case.');
        }
        $sections = TestCase::where('page_id', $page->id)->distinct()->pluck('section');
        return view('dashboard.test_cases.edit', compact('project', 'page', 'testCase','sections'));
    }

    /**
     * Update the specified test case in storage.
     */
    public function update(Request $request, Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to update this test case.');
        }

        $validated = $request->validate([
            'section' => 'nullable|string|max:255',
            'new_section' => 'nullable|string|max:255',
            'test_case_id' => 'required|string|max:255',
            'test_title' => 'required',
            'description' => 'required',
            'test_status' => 'nullable|in:pending,pass,fail',
            'comments' => 'nullable|string',
        ]);

        $section = $validated['section'] ?? $validated['new_section'];  // If section is not selected, use new_section
        if (!$section && !$testCase->section) {
            return redirect()->back()->with('error', 'Section is required. Please select or create a section.');
        }

        $testCase->update([
            'section' => $section,
            'test_case_id' => $validated['test_case_id'],
            'test_title' => $validated['test_title'],
            'description' => $validated['description'],
            'test_status' => $validated['test_status'] ?? $testCase->test_status,
            'comments' => $validated['comments'],
            'tested_by' => auth()->id(),
        ]);

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case updated successfully.');
    }

    /**
     * Remove the specified test case from storage.
     */
    public function delete(Request $request, Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to delete this test case.');
        }

        $request->validate([
            'test_case_confirmation' => ['required', 'string', function ($attribute, $value, $fail) use ($testCase) {
                if ($value !== $testCase->test_title) {
                    $fail('The confirmation text does not match the test case title.');
                }
            }],
        ]);

        $testCase->delete();

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case deleted successfully.');
    }

    public function updateStatus(Request $request, Project $project, Page $page, $id)
    {
        // Authorization check
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to update the test case status.');
        }

        // Validate input
        $validated = $request->validate([
            'status' => 'required|in:0,1,2', // 0 for pending, 1 for pass, 2 for fail
            'comments' => 'nullable|string',
        ]);

        // Find the test case
        $testCase = TestCase::find($id);
        if (!$testCase) {
            return redirect()->back()->with('error', 'Test case not found.');
        }

        // Update the test case
        $testCase->update([
            'test_status' => $validated['status'],
            'comments' => $validated['comments'],
            'tested_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Test case status updated successfully!');
    }


}
