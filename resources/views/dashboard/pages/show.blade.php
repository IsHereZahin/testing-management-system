@extends('dashboard/master')

@section('content')
<div class="container py-4">

    <!-- Project Title Section -->
    <h4 class="font-weight-bold text-center mb-0">Page Details for Project: {{ $project->name }}</h4>

    <!-- Page Name Section -->
    <div class="mb-4">
        <h6 class="text-muted">Page Name</h6>
        <p class="fw-bold">{{ $page->name }}</p>
    </div>

    <!-- Requirements Section -->
    <div class="mb-4">
        <h6 class="text-muted">Requirements</h6>
        <div class="border p-3 rounded">
            <div style="white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word;">
                {!! $page->requirement !!}
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('page.index', $project->id) }}" class="btn btn-outline-secondary btn-sm">Back to Pages</a>
    </div>
</div>
@endsection
