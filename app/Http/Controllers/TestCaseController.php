<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use App\Models\Project;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\TestCasesExport;
use Maatwebsite\Excel\Facades\Excel;

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
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to view test cases.');
        }

        $testCases = TestCase::where('page_id', $page->id)->get();
        return view('dashboard.test_cases.index', compact('testCases', 'project', 'page'));
    }

    /**
     * Show the form for creating a new test case.
     */
    public function create(Project $project, Page $page)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to create test cases.');
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
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to save test cases.');
        }

        $validated = $request->validate([
            'test_title' => 'required|string|max:255',
            'description' => 'required',
            'test_status' => 'nullable',
            'comments' => 'nullable|string',
        ]);

        $section = $request->section ?: $request->new_section;
        if (!$section) {
            return redirect()->back()->withErrors(['section' => 'Please select or create a section.']);
        }

        // Generate the test_case_id
        $projectInitials = strtoupper(substr($project->name, 0, 3));
        $pageId = $page->id;
        $testCaseId = $projectInitials . $pageId . (TestCase::max('id') + 1);

        TestCase::create([
            'section' => $section,
            'test_case_id' => $testCaseId,
            'test_title' => $validated['test_title'],
            'description' => $validated['description'],
            'comments' => $validated['comments'] ?? null,
            'tested_by' => auth()->id(),
            'page_id' => $page->id,
        ]);

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case added successfully.');
    }

    /**
     * Display the specified test case.
     */
    public function show(Project $project, Page $page, TestCase $test)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to view this test case.');
        }

        return view('dashboard.test_cases.show', compact('test', 'project', 'page'));
    }

    /**
     * Show the form for editing the specified test case.
     */
    public function edit(Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to edit test cases.');
        }

        $sections = TestCase::where('page_id', $page->id)->distinct()->pluck('section');
        return view('dashboard.test_cases.edit', compact('project', 'page', 'testCase', 'sections'));
    }

    /**
     * Update the specified test case in storage.
     */
    public function update(Request $request, Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to update this test case.');
        }

        $validated = $request->validate([
            'section' => 'nullable|string|max:255',
            'new_section' => 'nullable|string|max:255',
            // 'test_case_id' => 'required|string|max:255',
            'test_title' => 'required',
            'description' => 'required',
            'test_status' => 'nullable|in:pending,pass,fail',
            'comments' => 'nullable|string',
        ]);

        $section = $validated['section'] ?? $validated['new_section'];
        if (!$section && !$testCase->section) {
            return redirect()->back()->with('error', 'Section is required. Select or create a section.');
        }

        $testCase->update([
            'section' => $section,
            'test_title' => $validated['test_title'],
            'description' => $validated['description'],
            'test_status' => $validated['test_status'] ?? $testCase->test_status,
            'comments' => $validated['comments'],
            'tested_by' => auth()->id(),
        ]);

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('info', 'Test case updated successfully.');
    }

    /**
     * Remove the specified test case from storage.
     */
    public function delete(Request $request, Project $project, Page $page, TestCase $testCase)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to delete this test case.');
        }

        $request->validate([
            'test_case_confirmation' => ['required', 'string', function ($attribute, $value, $fail) {
                if (strtolower($value) !== 'delete task') {
                    $fail('Confirmation text must be "delete task".');
                }
            }],
        ]);

        $testCase->delete();

        return redirect()->route('test.index', ['project' => $project->id, 'page' => $page->id])
                        ->with('success', 'Test case removed successfully.');
    }

    public function updateStatus(Request $request, Project $project, Page $page, $id)
    {
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to update test case status.');
        }

        $validated = $request->validate([
            'status' => 'required|in:0,1,2', // 0 for pending, 1 for pass, 2 for fail
            'comments' => 'nullable|string',
        ]);

        $testCase = TestCase::find($id);
        if (!$testCase) {
            return redirect()->back()->with('error', 'Test case not found.');
        }

        $testCase->update([
            'test_status' => $validated['status'],
            'comments' => $validated['comments'],
            'tested_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Test case status updated successfully.');
    }

    public function resetAllTestCases(Request $request, Project $project)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('warning', 'You must be logged in to perform this action.');
        }

        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('warning', 'Access denied: Unauthorized to update test case status.');
        }

        $request->validate([
            'confirmation' => 'required|in:'.$project->name,
        ]);

        // Check at least one checkbox is selected (reset status or reset comments)
        if (!$request->has('reset_status') && !$request->has('reset_comments')) {
            return redirect()->back()->with('danger', 'Please select at least one option (Status or Comments) to reset.');
        }

        $currentUserId = auth()->id();

        $testCases = TestCase::whereHas('page', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();

        // Loop each test case
        foreach ($testCases as $testCase) {
            if ($request->has('reset_status')) {
                $testCase->test_status = 0; // Reset status to 'Pending'
            }

            if ($request->has('reset_comments')) {
                $testCase->comments = null;
            }

            // Update 'tested_by' user's ID
            $testCase->tested_by = $currentUserId;

            $testCase->save();
        }
        return redirect()->back()->with('success', 'Test case statuses and/or comments have been reset.');
    }

    public function export(Request $request, Project $project)
    {
        // Check if the user has permission to export
        if (!$this->authorizeProjectAccess($project)) {
            return redirect()->route('projects')->with('error', 'Access denied: Unauthorized to export test cases.');
        }

        // Validate the request
        $validated = $request->validate([
            'format' => 'required|in:csv,xls',
            'columns' => 'required|array',
            'columns.*' => 'string|in:test_case_id,test_title,description,test_status,comments,tested_by',
        ]);

        // Create export instance
        $export = new TestCasesExport($project, $validated['columns']);

        // Format file name
        $fileName = Str::slug($project->name) . ($validated['format'] == 'csv' ? '.csv' : '.xlsx');

        // Export based on format
        return Excel::download($export, $fileName);
    }
}
