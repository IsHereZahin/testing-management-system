@extends('dashboard/master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold text-center">Create New Project</h4>
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
                    <form action="{{ route('project.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Project Name</label>
                            <input type="text" id="name" name="name" class="form-control border p-3" placeholder="Enter project name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Project Description</label>
                            <textarea id="description" name="description" class="form-control border p-3" rows="5" placeholder="Briefly describe the project." required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Add a field for selecting testers -->
                        <div class="form-group mb-3">
                            <label for="testers" class="form-label">Assign Testers</label>
                            <select id="testers" name="testers[]" class="form-control border p-3" multiple required>
                                @foreach($testers as $tester)
                                    <option value="{{ $tester->id }}">{{ $tester->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold CTRL to select multiple testers.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('projects') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Create Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
