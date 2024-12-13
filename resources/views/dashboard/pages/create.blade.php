@extends('dashboard/master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold text-center">Create New Page for Project: {{ $project->name }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('page.store', $project->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Page Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control border p-3"
                                placeholder="Enter page name"
                                value="{{ old('name') }}"
                                required
                            >
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('page.index', $project->id) }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Create Page</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
