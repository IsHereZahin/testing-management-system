<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    public function projects()
    {
        $user = auth()->user();

        // Fetch projects based on user role
        if ($user->role === 'super-admin') {
            $projects = Project::all();
        } elseif ($user->role === 'tester') {
            $projects = $user->projects;
        }

        return view('dashboard.projects.index', compact('projects'));
    }

    public function create()
    {
        $testers = User::where('role', 'tester')->get();

        return view('dashboard.projects.create', compact('testers'));
    }

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

        // Assign testers to the project (many-to-many relationship)
        $project->testers()->sync($request->testers);

        return redirect()->route('projects')->with('success', 'Project created successfully.');
    }



    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $users = User::where('role', 'tester')->get();
        return view('dashboard.projects.edit', compact('project', 'users'));
    }

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

        // Sync testers: this will update the pivot table with selected testers
        $project->testers()->sync($request->testers);

        return redirect()->route('projects')->with('success', 'Project updated successfully!');
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);

        if (auth()->user()->role !== 'super-admin') {
            return redirect()->route('projects')->with('error', 'You are not authorized to delete this project.');
        }

        DB::transaction(function () use ($project) {
            foreach ($project->pages as $page) {
                $page->testCases()->delete();
            }
            $project->pages()->delete();
            $project->testers()->detach();
            $project->delete();
        });

        return redirect()->route('projects')->with('success', 'Project and its related pages, test cases, and users deleted successfully.');
    }
}
