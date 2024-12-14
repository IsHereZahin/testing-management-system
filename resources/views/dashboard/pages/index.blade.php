@extends('dashboard.master')

@section('content')
<div class="container py-4">
    <h2>Pages for Project: {{ $project->name }}</h2>

    @if(auth()->user()->role === 'super-admin' || $project->testers->contains(auth()->user()))
        <a href="{{ url('/project/' . $project->id . '/page/create') }}" class="btn btn-primary">Create Page</a>
    @endif

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
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Update</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">View</th>
                                        @can('edit-page')
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $page)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ $page->creator->avatar_url ?? asset('./assets/img/team-4.jpg') }}" class="avatar avatar-sm me-3 border-radius-lg" alt="user1">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $page->creator->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $page->creator->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $page->name }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $page->updated_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-success">Active</span> <!-- Use dynamic status if available -->
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('test.index', ['project' => $project->id, 'page' => $page->id]) }}" data-bs-toggle="tooltip" title="View Test" class="btn btn-info btn-sm p-2 text-white" data-toggle="tooltip" data-original-title="Show Test Cases">
                                                <i class="fas fa-eye"></i> Tests
                                            </a>
                                        </td>
                                        {{-- <td class="align-middle text-center">
                                            <a href="{{ route('page.show', ['project' => $project->id, 'page' => $page->id]) }}" class="btn btn-info btn-sm p-2 text-white" data-toggle="tooltip" data-original-title="Show Page">
                                                <i class="fas fa-eye"></i> Show
                                            </a>
                                        </td> --}}
                                        <td class="d-flex gap-2">

                                            @can('edit-page')
                                                <a href="{{ url('/project/' . $project->id . '/page/' . $page->id . '/edit') }}" class="btn btn-warning d-flex align-items-center justify-content-center p-2" title="Edit Page">
                                                    <i class="material-symbols-rounded fs-4">edit</i> <!-- Font size increased -->
                                                </a>
                                            @endcan

                                            @can('delete-page')
                                            <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center p-2"
                                                    data-bs-toggle="modal" title="Delete Page" data-bs-target="#deletePageModal-{{ $page->id }}">
                                                <i class="material-symbols-rounded fs-4">delete</i>
                                            </button>

                                            <div class="modal fade" id="deletePageModal-{{ $page->id }}" tabindex="-1" aria-labelledby="deletePageModalLabel-{{ $page->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deletePageModalLabel-{{ $page->id }}">Delete Page</h5>
                                                            <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close">X</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                This page contains <strong>{{ $page->testCases->count() }}</strong> test case(s). Deleting it will remove all <br>associated data.
                                                            </p>
                                                            <p class="text-danger">
                                                                This action cannot be undone.
                                                            </p>
                                                            <form action="{{ route('page.destroy', [$project->id, $page->id]) }}" method="POST" id="deletePageForm">
                                                                @csrf
                                                                @method('DELETE')

                                                                <div class="mb-3">
                                                                    <label for="pageNameConfirmation-{{ $page->id }}" class="form-label">
                                                                        To confirm, type "<strong>{{ $page->name }}</strong>" in the box below:
                                                                    </label>
                                                                    <input type="text" name="page_name_confirmation" id="pageNameConfirmation-{{ $page->id }}"
                                                                        class="form-control border p-2" placeholder="{{ $page->name }}" required>
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




                                        </td>
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
@endsection
