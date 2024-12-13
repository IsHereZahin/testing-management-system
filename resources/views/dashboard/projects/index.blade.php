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
                            <p class="text-sm mb-0 text-capitalize">Project</p>
                            <h4 class="mb-0">
                                <a href="{{ url('/project/' . $project->id . '/pages') }}">{{ $project->name }}</a>
                            </h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                            <a href="{{ url('/project/' . $project->id . '/pages') }}">
                                <i class="material-symbols-rounded opacity-10">folder</i>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3 d-flex justify-content-between">
                    <p class="text-sm">{{ $project->description }}</p>

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

                        <!-- Delete Icon -->
                        <form action="{{ route('project.delete', $project->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center p-2" title="Delete Project" onclick="return confirm('Are you sure you want to delete this project?');">
                                <i class="material-symbols-rounded fs-4">delete</i> <!-- Font size increased -->
                            </button>
                        </form>
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
