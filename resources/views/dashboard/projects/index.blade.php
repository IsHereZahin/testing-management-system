@extends('dashboard/master')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <!-- Header Section -->
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bold">Projects</h3>
            @if(auth()->user()->role === 'super-admin')
                <p class="mb-4 text-muted">
                    As a Super Admin, you can manage all projects from this section.
                </p>
            @elseif(auth()->user()->role === 'tester')
                <p class="mb-4 text-muted">
                    You have access to view and collaborate on assigned projects.
                </p>
            @endif
        </div>

        <!-- Projects Loop -->
        @forelse($projects as $project)
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm cursor-pointer mb-0 text-capitalize">Project</p>
                            <h4 class="mb-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Description: {{ $project->description }}">
                                <a href="{{ url('/project/' . $project->id . '/pages') }}">{{ $project->name }}</a>
                            </h4>
                        </div>
                        <div class="icon icon-md icon-shape cursor-pointer bg-gradient-dark shadow-dark text-center border-radius-lg" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Description: {{ $project->description }}">
                            <a href="{{ url('/project/' . $project->id . '/pages') }}">
                                <i class="material-symbols-rounded opacity-10">folder</i>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3 d-flex justify-content-between">
                    {{-- <p class="text-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $project->description }}">
                        {{ Str::words($project->description, 4) }}
                    </p> --}}

                    <!-- Display related testers -->
                    <div class="avatar-group mt-2">
                        @foreach($project->testers as $tester)
                            <a href="javascript:;" class="avatar avatar-xs rounded-circle"
                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="{{ $tester->name }}">
                                <img src="{{ $tester->avatar_url ? $tester->avatar_url : asset('./assets/img/team-4.jpg') }}" alt="{{ $tester->name }}">
                            </a>
                        @endforeach
                    </div>

                    @if(auth()->user()->role === 'super-admin')
                    <div class="d-flex gap-2">
                        <!-- Edit Icon -->
                        <a href="{{ route('project.edit', $project->id) }}" class="btn btn-warning d-flex align-items-center justify-content-center p-2" title="Edit Project">
                            <i class="material-symbols-rounded fs-4">edit</i> <!-- Font size increased -->
                        </a>

                        <!-- Delete Project Button -->
                        <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center p-2"
                                title="Delete Project" data-bs-toggle="modal" data-bs-target="#deleteProjectModal-{{ $project->id }}">
                            <i class="material-symbols-rounded fs-4">delete</i>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="deleteProjectModal-{{ $project->id }}" tabindex="-1" aria-labelledby="deleteProjectModalLabel-{{ $project->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteProjectModalLabel-{{ $project->id }}">Delete Project</h5>
                                        <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                                    </div>
                                    <div class="modal-body">
                                        <p>
                                            This project contains <strong>{{ $project->pages->count() }}</strong> pages and
                                            <strong>{{ $project->pages->sum(fn($page) => $page->testCases->count()) }}</strong> test cases.
                                            Deleting it will remove all associated data.
                                        </p>
                                        <p class="text-danger">
                                            This action cannot be undone.
                                        </p>
                                        <form action="{{ route('project.delete', $project->id) }}" method="POST" id="deleteProjectForm">
                                            @csrf
                                            @method('DELETE')

                                            <div class="mb-3">
                                                <label for="projectNameConfirmation-{{ $project->id }}" class="form-label">
                                                    To confirm, type "<strong>{{ $project->name }}</strong>" in the box below:
                                                </label>
                                                <input type="text" name="project_name_confirmation" id="projectNameConfirmation-{{ $project->id }}"
                                                    class="form-control border p-2" placeholder="{{ $project->name }}" required>
                                            </div>

                                            <button type="submit" class="btn btn-danger w-100">
                                                Confirm Deletion
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-center text-muted">No projects available.</p>
        </div>
        @endforelse

        <!-- Add Project -->
        @if(auth()->user()->role === 'super-admin')
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-2 ps-3 text-center">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">add</i>
                    </div>
                </div>
                <div class="card-footer p-2 ps-3 text-center">
                    <a href="{{ route('project.create') }}" class="btn bg-gradient-dark text-center">Add Project</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
