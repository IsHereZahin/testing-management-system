@extends('dashboard.master')

@section('content')
<div class="container py-4">
    <h2>Test Cases for Page: {{ $page->name }}</h2>

    @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
        <a href="{{ url('/project/' . $project->id . '/' . $page->id . '/test/create') }}" class="btn btn-primary">Create Test Case</a>
    @endif

    <!-- Loop through each section and create a table for each -->
    @foreach($testCases->groupBy('section') as $section => $testCasesInSection)
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3"><span class="opacity-7">Section: </span>{{ $section }}</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Activity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comments</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Test Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Step Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testCasesInSection as $testCase)
                                    <tr>
                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ \App\Models\User::find($testCase->tested_by)->name ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-secondary mb-0">{{ $testCase->updated_at->format('d/m/Y h:i A') }}</p>
                                        </td>
                                        <td class="align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $testCase->test_case_id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ Str::limit($testCase->description, 50) }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ Str::limit($testCase->comments, 50) }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm @if($testCase->test_status == 'pass') bg-gradient-success @elseif($testCase->test_status == 'fail') bg-gradient-danger @else bg-gradient-warning @endif">
                                                {{ ucfirst($testCase->test_status) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm @if($testCase->step_status == 'pass') bg-gradient-success @elseif($testCase->step_status == 'fail') bg-gradient-danger @else bg-gradient-warning @endif">
                                                {{ ucfirst($testCase->step_status) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('test.show', ['project' => $project->id, 'page' => $page->id, 'testCase' => $testCase->id]) }}" class="btn btn-info btn-sm p-2 text-white" data-toggle="tooltip" data-original-title="View Test Case">
                                                <i class="fas fa-eye"></i> View
                                            </a>

                                            <a href="{{ route('test.edit', ['project' => $project->id, 'page' => $page->id, 'testCase' => $testCase->id]) }}" class="btn btn-warning btn-sm p-2 text-white" data-toggle="tooltip" data-original-title="Edit Test Case">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            <form action="{{ url('/project/' . $project->id . '/' . $page->id . '/test/' . $testCase->id . '/delete') }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm p-2" data-toggle="tooltip" data-original-title="Delete Test Case" onclick="return confirm('Are you sure you want to delete this test case?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
