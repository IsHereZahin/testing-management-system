@extends('dashboard/master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold text-center">Edit Page for Project: {{ $project->name }}</h4>
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
                    <form action="{{ route('page.update', ['project' => $project->id, 'page' => $page->id]) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- This specifies the method for updating the resource -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Page Name</label>
                            <input type="text" id="name" name="name" class="form-control border p-3" placeholder="Enter page name" value="{{ old('name', $page->name) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Requirement</label>
                            <textarea id="requirement" name="requirement" class="ckeditor border p-3" placeholder="Enter test case requirement" required>{{ old('requirement', $page->requirement) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('page.index', $project->id) }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-warning">Update Page</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
