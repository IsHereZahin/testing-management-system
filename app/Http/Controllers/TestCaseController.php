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
            'description' => 'required|string',
            'steps' => 'required|string',
            'expected_result' => 'required|string',
            'step_status' => 'nullable|string',
            'test_status' => 'nullable|in:pending,pass,fail',
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
            'description' => $validated['description'],
            'steps' => $validated['steps'],
            'expected_result' => $validated['expected_result'],
            'step_status' => $validated['step_status'] ?? 'Not tested',
            'test_status' => $validated['test_status'] ?? 'pending',
            'comments' => $validated['comments'],
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

        // Validate the form data
        $validated = $request->validate([
            // 'section' validation is now handled conditionally based on 'new_section'
            'section' => 'nullable|string|max:255', // Allow section to be empty if new_section is filled
            'new_section' => 'nullable|string|max:255', // Allow new_section to be filled if no section is selected
            'test_case_id' => 'required|string|max:255',
            'description' => 'required|string',
            'steps' => 'required|string',
            'expected_result' => 'required|string',
            'step_status' => 'nullable|string',
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
            'description' => $validated['description'],
            'steps' => $validated['steps'],
            'expected_result' => $validated['expected_result'],
            'step_status' => $validated['step_status'] ?? $testCase->step_status,
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
    public function delete(Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'You are not authorized to delete this test case.');
        }

        $testCase->delete();

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case deleted successfully.');
    }
}
