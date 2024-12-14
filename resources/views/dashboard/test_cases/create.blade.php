@extends('dashboard.master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="font-weight-bold text-center">
                        Create New Test Case for Page: {{ $page->name }}
                    </h4>
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

                    <form action="{{ url('/project/' . $project->id . '/' . $page->id . '/test/store') }}" method="POST">
                        @csrf

                        <!-- Section Selection or Creation -->
                        <div class="form-group mb-3">
                            <label for="section" class="form-label">Section</label>
                            <select name="section" id="section" class="form-control border p-3">
                                <option value="">Select a Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section }}">{{ $section }}</option>
                                @endforeach
                            </select>
                            <div class="text-center my-2">or</div>
                            <input type="text" id="new_section" name="new_section" class="form-control border p-3" placeholder="Create a New Section" value="{{ old('new_section') }}"
                                @if($project->testCases->count() > 0)
                                    disabled
                                @endif
                            >
                        </div>

                        <script>
                            document.getElementById('section').addEventListener('change', function () {
                                var sectionDropdown = document.getElementById('section');
                                var newSectionInput = document.getElementById('new_section');

                                // Enable/Disable the new section input field based on the dropdown selection
                                if (sectionDropdown.value !== "") {
                                    newSectionInput.disabled = true;
                                    newSectionInput.value = "";
                                } else {
                                    newSectionInput.disabled = false;
                                }
                            });

                            // Ensure the new section field is enabled if no test cases exist
                            @if($project->testCases->count() === 0)
                                document.getElementById('new_section').disabled = false;
                            @endif
                        </script>

                        <!-- Test Case ID -->
                        <div class="form-group mb-3">
                            <label for="test_case_id" class="form-label">Test Case ID</label>
                            <input type="text" id="test_case_id" name="test_case_id" class="form-control border p-3" placeholder="Enter test case ID" value="{{ old('test_case_id') }}" required>
                        </div>

                        <!-- test_title -->
                        <div class="form-group mb-3">
                            <label for="test_title" class="form-label">Test test_title</label>
                            <input type="text" id="test_title" name="test_title" class="form-control border p-3" placeholder="Enter test title" value="{{ old('test_title') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="ckeditor border p-3" placeholder="Enter test case description" required>{{ old('description') }}</textarea>
                        </div>

                        {{-- <!-- Comments -->
                        <div class="form-group mb-3">
                            <label for="comments" class="form-label">Comments</label>
                            <textarea id="comments" name="comments" class="ckeditor form-control border p-3" placeholder="Optional comments">{{ old('comments') }}</textarea>
                        </div> --}}

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('/project/' . $project->id . '/' . $page->id . '/tests') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Create Test Case</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
