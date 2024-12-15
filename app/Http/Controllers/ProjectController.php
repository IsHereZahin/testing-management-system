<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    /**
     * Display a list of projects available to the authenticated user.
     */
    public function projects()
    {
        $user = auth()->user();

        // Retrieve projects based on the user role
        if ($user->role === 'super-admin') {
            $projects = Project::all();
        } elseif ($user->role === 'tester') {
            $projects = $user->projects;
        }

        return view('dashboard.projects.index', compact('projects'));
    }

    /**
     * Show the form to create a new project.
     */
    public function create()
    {
        $testers = User::where('role', 'tester')->get();

        return view('dashboard.projects.create', compact('testers'));
    }

    /**
     * Store a newly created project in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'testers' => 'required|array',
            'testers.*' => 'exists:users,id',
        ]);

        // Create the project
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Assign testers to the project
        $project->testers()->sync($request->testers);

        return redirect()->route('projects')->with('success', 'Project has been successfully created.');
    }

    /**
     * Show the form to edit an existing project.
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $users = User::where('role', 'tester')->get();

        return view('dashboard.projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified project in the database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'testers' => 'array',
            'testers.*' => 'exists:users,id',
        ]);

        $project = Project::findOrFail($id);

        // Update project details
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Update testers associated with the project
        $project->testers()->sync($request->testers);

        return redirect()->route('projects')->with('info', 'Project has been updated successfully.');
    }

    /**
     * Delete the specified project and its related data.
     */
    // public function delete(Request $request, $id)
    // {
    //     $project = Project::findOrFail($id);

    //     if (auth()->user()->role !== 'super-admin') {
    //         return redirect()->route('projects')->with('error', 'You are not authorized to delete this project.');
    //     }

    //     $request->validate([
    //         'project_name_confirmation' => ['required', 'string', function ($attribute, $value, $fail) use ($project) {
    //             if ($value !== $project->name) {
    //                 $fail('The confirmation text does not match the project name.');
    //             }
    //         }],
    //     ]);

    //     DB::transaction(function () use ($project) {
    //         foreach ($project->pages as $page) {
    //             $page->testCases()->delete();
    //         }
    //         $project->pages()->delete();
    //         $project->testers()->detach();
    //         $project->delete();
    //     });

    //     return redirect()->route('projects')
    //     ->with('success', 'The project has been successfully deleted.')
    //     ->with('danger', 'All pages and related data have been permanently removed.');
    // }
    public function delete(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if (auth()->user()->role !== 'super-admin') {
            return redirect()->route('projects')->with('error', 'You are not authorized to delete this project.');
        }

        $request->validate([
            'project_name_confirmation' => ['required', 'string', function ($attribute, $value, $fail) use ($project) {
                if ($value !== $project->name) {
                    $fail('The confirmation text does not match the project name.');
                }
            }],
        ]);

        // Get the number of pages and test cases before deletion
        $pagesCount = $project->pages->count();
        $testCasesCount = $project->pages->sum(function($page) {
            return $page->testCases->count();
        });

        DB::transaction(function () use ($project) {
            foreach ($project->pages as $page) {
                $page->testCases()->delete();
            }
            $project->pages()->delete();
            $project->testers()->detach();
            $project->delete();
        });

        return redirect()->route('projects')
            ->with('success', "The project has been successfully deleted.")
            ->with('danger', "{$pagesCount} pages and {$testCasesCount} test cases have been permanently removed. This action is irreversible.");
    }
}
