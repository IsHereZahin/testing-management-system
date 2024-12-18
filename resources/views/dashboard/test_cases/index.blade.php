@extends('dashboard.master')

@section('content')
@if($testCases->isEmpty())
<div class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
        <a href="{{ url('/project/' . $project->id . '/' . $page->id . '/test/create') }}" class="btn bg-gradient-dark text-center mb-3">Create Test</a>
    @endif
    <h5>No test found for this page.</h5>
    <p>You can create one by clicking the "Create Test" button above.</p>
    <a href="{{ url('/project/' . $project->id . '/pages') }}" style="text-decoration: none;">
        Back
    </a>
</div>
@else
<div class="container py-4">
    <h5>
        Test Cases for Page:
        <a href="{{ url('/project/' . $project->id . '/pages') }}" style="color: #007bff; text-decoration: none;">
            {{ $page->name }}
        </a>
    </h5>

    @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
        <a href="{{ url('/project/' . $project->id . '/' . $page->id . '/test/create') }}" class="btn bg-gradient-dark text-center">Create Test</a>
    @endif

    <!-- Export Button -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">Export</button>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Test Cases</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/projects/{{ $project->id }}/test-cases/export" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" name="project_name" placeholder="Enter project name" value="{{ $project->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Export Format</label>
                            <div>
                                <input type="radio" id="formatCsv" name="format" value="csv" checked>
                                <label for="formatCsv">CSV</label>
                            </div>
                            <div>
                                <input type="radio" id="formatXls" name="format" value="xls">
                                <label for="formatXls">XLS</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Columns</label>
                            <div>
                                <input type="checkbox" name="columns[]" value="test_case_id" checked> Test Case ID
                            </div>
                            <div>
                                <input type="checkbox" name="columns[]" value="test_title" checked> Title
                            </div>
                            <div>
                                <input type="checkbox" name="columns[]" value="description"> Description
                            </div>
                            <div>
                                <input type="checkbox" name="columns[]" value="test_status" checked> Test Status
                            </div>
                            <div>
                                <input type="checkbox" name="columns[]" value="comments"> Comments
                            </div>
                            <div>
                                <input type="checkbox" name="columns[]" value="tested_by"> Tested By
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Export</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Test Case ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Test Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
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
                                            <p class="text-xs font-weight-bold mb-0">{{ $testCase->test_title }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span
                                                class="badge badge-sm
                                                    @if($testCase->test_status == 0)
                                                        bg-gradient-warning  // Yellow for pending
                                                    @elseif($testCase->test_status == 1)
                                                        bg-gradient-success  // Green for pass
                                                    @else
                                                        bg-gradient-danger  // Red for fail
                                                    @endif
                                                    cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#statusChangeModal-{{ $testCase->id }}">
                                                @if($testCase->test_status == 0)
                                                    Pending
                                                @elseif($testCase->test_status == 1)
                                                    Pass
                                                @else
                                                    Fail
                                                @endif
                                            </span>
                                        </td>

                                        <!-- Status Change Modal -->
                                        <div class="modal fade" id="statusChangeModal-{{ $testCase->id }}" tabindex="-1" aria-labelledby="statusChangeModalLabel-{{ $testCase->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusChangeModalLabel-{{ $testCase->id }}">Change Test Case Status</h5>
                                                        <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('test.update-status', ['project' => $project->id, 'page' => $page->id, 'id' => $testCase->id]) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label for="status-{{ $testCase->id }}" class="form-label">Test Status</label>
                                                                <select name="status" id="status-{{ $testCase->id }}" class="border p-2 form-select" required>
                                                                    <option value="0" @if($testCase->test_status == 0) selected @endif>Pending</option>
                                                                    <option value="1" @if($testCase->test_status == 1) selected @endif>Pass</option>
                                                                    <option value="2" @if($testCase->test_status == 2) selected @endif>Fail</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="comments-{{ $testCase->id }}" class="form-label">Comments</label>
                                                                <textarea name="comments" id="comments-{{ $testCase->id }}" class="ckeditor form-control" rows="3" placeholder="Add comments..." required>{{ $testCase->comments ?? '' }}</textarea>
                                                            </div>

                                                            <div class="d-flex justify-content-between">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn bg-gradient-dark text-center">Save</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Status Change Modal -->

                                        <td class="align-middle text-center">
                                            <div class="d-flex gap-2">
                                                <!-- Button to Trigger Modal -->
                                                @php
                                                    $hasComment = !empty($testCase->comments);
                                                @endphp
                                                <div class="btn @if($hasComment) btn-info @else btn-secondary @endif btn-sm p-2 text-white"
                                                    data-bs-toggle="modal" data-bs-target="#testCaseModal-{{ $testCase->id }}"
                                                    data-bs-toggle="tooltip" data-bs-original-title="View Test Case">
                                                    <i class="material-symbols-rounded fs-4">info</i>
                                                </div>

                                                <!-- Modal Structure -->
                                                <div class="modal fade" id="testCaseModal-{{ $testCase->id }}" tabindex="-1" aria-labelledby="testCaseModalLabel-{{ $testCase->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-end modal-dialog-custom">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="testCaseModalLabel-{{ $testCase->id }}">Test Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="text-align: left;">
                                                                <h5 style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word;">{{ $testCase->test_title }}</h5>
                                                                <h6>Description:</h6>
                                                                <div style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; margin: -50px 0px;">
                                                                    {!! old('description', $testCase->description) !!}
                                                                </div>

                                                                @if($testCase->comments)
                                                                    <h6>Comment:</h6>
                                                                    <div style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; margin: -50px 0px;">
                                                                        {!! old('comments', $testCase->comments) !!}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="{{ route('test.edit', ['project' => $project->id, 'page' => $page->id, 'testCase' => $testCase->id]) }}" class="btn btn-warning d-flex align-items-center justify-content-center p-2" title="Edit Test Case">
                                                    <i class="material-symbols-rounded fs-4">edit</i>
                                                </a>

                                                <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center p-2"
                                                        data-bs-toggle="modal" title="Delete Test Case" data-bs-target="#deleteTestCaseModal-{{ $testCase->id }}">
                                                    <i class="material-symbols-rounded fs-4">delete</i>
                                                </button>

                                                <div class="modal fade" id="deleteTestCaseModal-{{ $testCase->id }}" tabindex="-1" aria-labelledby="deleteTestCaseModalLabel-{{ $testCase->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteTestCaseModalLabel-{{ $testCase->id }}">Delete Test</h5>
                                                                <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>
                                                                    Are you sure you want to delete the test?
                                                                </p>
                                                                <p class="text-danger">This action cannot be undone.</p>
                                                                <form action="{{ route('test.delete', ['project' => $project->id, 'page' => $page->id, 'testCase' => $testCase->id]) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <div class="mb-3">
                                                                        <label for="testCaseConfirmation-{{ $testCase->id }}" class="form-label">
                                                                            To confirm, type "delete task" below:
                                                                        </label>
                                                                        <input type="text" name="test_case_confirmation" id="testCaseConfirmation-{{ $testCase->id }}"
                                                                            class="form-control border p-2" placeholder="delete task" required>
                                                                    </div>

                                                                    <button type="submit" class="btn btn-danger w-100">Confirm Deletion</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
<script>
document.getElementById('exportForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const params = new URLSearchParams();

    // Combine checkbox values for columns
    const selectedColumns = formData.getAll('columns').join(',');
    formData.delete('columns'); // Remove duplicate entries
    params.append('columns', selectedColumns);

    for (const pair of formData.entries()) {
        if (pair[0] !== 'columns') {
            params.append(pair[0], pair[1]);
        }
    }

    const url = `/export?${params.toString()}`;
    window.location.href = url; // Trigger download
});
</script>
<style>
.modal-dialog-custom {
    max-width: 70%;
}

@media (max-width: 768px) {
    .modal-dialog-custom {
        max-width: 95%;
    }
}
</style>
@endif
@endsection
