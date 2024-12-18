@extends('dashboard.master')

@section('content')
@if($pages->isEmpty())
<div class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
        <a href="{{ url('/project/' . $project->id . '/page/create') }}" class="btn bg-gradient-dark text-center">Create Page</a>
    @endif
    <h5>No pages found for this project.</h5>
    <p>You can create a new page by clicking the "Create Page" button above.</p>
</div>
@else
<div class="container py-4">
    <div class="d-flex justify-content-between mb-4">
        <!-- Left Side: Project Name and Create Page Button -->
        <div>
            <h5>
                Pages for Project:
                <a href="{{ url('/projects') }}" style="color: #007bff; text-decoration: none;">
                    {{ $project->name }}
                </a>
            </h5>

            @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
                <a href="{{ url('/project/' . $project->id . '/page/create') }}" class="btn bg-gradient-dark text-center">Create Page</a>
            @endif
        </div>

        <!-- Right Side: Reset Button -->
        <div>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetModal">
                Reset All
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetModalLabel">Reset All Tests Data</h5>
                    <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <!-- Warning Message -->
                    <div class="alert alert-warning" role="alert">
                        <strong>Warning:</strong> Resetting test case status and comments cannot be undone. Please proceed with caution.
                    </div>

                    <form action="{{ route('test.resetAll', $project->id) }}" method="POST">
                        @csrf

                        <!-- Checkbox for Status Reset -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="resetStatus" name="reset_status">
                                <label class="form-check-label" for="resetStatus">
                                    Reset Status
                                </label>
                            </div>
                        </div>

                        <!-- Checkbox for Comment Reset -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="resetComments" name="reset_comments">
                                <label class="form-check-label" for="resetComments">
                                    Reset Comments
                                </label>
                            </div>
                        </div>

                        <!-- Confirmation -->
                        <div class="mb-3">
                            <label for="confirmation" class="form-label">
                                To confirm, type "{{ $project->name }}" below:
                            </label>
                            <input type="text" name="confirmation" id="projectNameConfirmation-{{ $project->id }}"
                                   class="form-control border p-2" placeholder="{{ $project->name }}" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger">Reset All Test Cases</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table displaying all pages -->
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">All Pages</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creator</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Page</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">View</th>
                                        @can('edit-page')
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $page)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                {{-- <div>
                                                    <img src="{{ $page->creator->avatar_url ?? asset('./assets/img/team-4.jpg') }}" class="avatar avatar-sm me-3 border-radius-lg" alt="user1">
                                                </div> --}}
                                                <div class="d-flex flex-column align-items-center">
                                                    <h6 class="text-xs font-weight-bold mb-1">{{ $page->creator->name }}</h6>
                                                    <span class="text-xs font-weight-bold mb-1">{{ $page->updated_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $page->name }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm" data-bs-toggle="tooltip"
                                            title="Total Tests: {{ $page->total_tests }}, Pending: {{ $page->pending_count }},
                                            Tested: {{ $page->pass_count + $page->fail_count }} ({{ $page->total_tests > 0 ? round(($page->pass_count + $page->fail_count) / $page->total_tests * 100, 2) : 0 }}%),
                                            Pass: {{ $page->pass_count }}, Fail: {{ $page->fail_count }}">
                                            <div class="d-flex flex-column align-items-center">
                                                <!-- Pending Tests -->
                                                {{-- <span class="text-xs font-weight-bold mb-1">
                                                    Pending: {{ $page->pending_count }}
                                                </span> --}}
                                                <!-- Pass and Fail Tests -->
                                                <span class="text-xs font-weight-bold mb-1">
                                                    Pass: {{ $page->pass_count }} | Fail: {{ $page->fail_count }}
                                                </span>
                                                <!-- Tested and Breakdown -->
                                                <span class="text-xs font-weight-bold mb-1">
                                                    Tested: {{ $page->pass_count + $page->fail_count }} / {{ $page->total_tests }}
                                                    ({{ $page->total_tests > 0 ? round(($page->pass_count + $page->fail_count) / $page->total_tests * 100, 2) : 0 }}%)
                                                </span>
                                                <!-- Progress Bar -->
                                                <div class="progress mt-2" style="width: 100%; height: 8px;">
                                                    @php
                                                        $total = $page->total_tests;
                                                        $passPercentage = $total > 0 ? ($page->pass_count / $total) * 100 : 0;
                                                        $failPercentage = $total > 0 ? ($page->fail_count / $total) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-gradient-success" role="progressbar"
                                                        style="width: {{ $passPercentage }}%;"
                                                        aria-valuenow="{{ $passPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-gradient-danger" role="progressbar"
                                                        style="width: {{ $failPercentage }}%;"
                                                        aria-valuenow="{{ $failPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>


                                        <td class="align-middle text-center">
                                            <a href="{{ route('test.index', ['project' => $project->id, 'page' => $page->id]) }}"
                                                data-bs-toggle="tooltip"
                                                title="View and manage the test cases related to this page"
                                                class="btn btn-info btn-sm p-2 text-white d-flex align-items-center justify-content-center">
                                                <span>Inspect Tests</span>
                                            </a>
                                        </td>

                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">

                                                {{-- <a href="{{ url('/project/' . $project->id . '/page/' . $page->id) }}" class="text-secondary font-weight-bold text-xs" title="Show Info Page">Info</a> --}}

                                                @can('edit-page')
                                                <a href="{{ url('/project/' . $project->id . '/page/' . $page->id . '/edit') }}" class="text-secondary font-weight-bold text-xs" title="Edit Page">Edit</a>
                                                @endcan

                                                @can('delete-page')
                                                <a type="button" class="text-secondary font-weight-bold text-xs" data-bs-toggle="modal" title="Delete Page" data-bs-target="#deletePageModal-{{ $page->id }}">Delete</a>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deletePageModal-{{ $page->id }}" tabindex="-1" aria-labelledby="deletePageModalLabel-{{ $page->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deletePageModalLabel-{{ $page->id }}">Delete Page</h5>
                                                                <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <p>
                                                                    This page contains <strong>{{ $page->testCases->count() }}</strong> test case(s).<br>Deleting it will remove all associated data.
                                                                </p>
                                                                <p class="text-danger">
                                                                    This action cannot be undone.
                                                                </p>
                                                                <form action="{{ route('page.destroy', [$project->id, $page->id]) }}" method="POST" id="deletePageForm">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <div class="mb-3 text-start">
                                                                        <label for="pageNameConfirmation-{{ $page->id }}" class="form-label">
                                                                            To confirm, type "<strong>{{ $page->name }}</strong>" in the box below:
                                                                        </label>
                                                                        <input type="text" name="page_name_confirmation" id="pageNameConfirmation-{{ $page->id }}" class="form-control border p-2" placeholder="{{ $page->name }}" required>
                                                                    </div>

                                                                    <button type="submit" class="btn btn-danger w-100">
                                                                        Confirm Deletion
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endcan

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
</div>
@endif
@endsection
