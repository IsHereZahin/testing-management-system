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
                            <input type="text" id="new_section" name="new_section" class="form-control border p-3" placeholder="Create a New Section" value="{{ old('new_section') }}" disabled>
                        </div>

                        <script>
                            document.getElementById('section').addEventListener('change', function () {
                                var sectionDropdown = document.getElementById('section');
                                var newSectionInput = document.getElementById('new_section');

                                // If a section is selected, disable the new section input field
                                if (sectionDropdown.value !== "") {
                                    newSectionInput.disabled = true;
                                    newSectionInput.value = "";  // Clear the new section input if select any section
                                } else {
                                    newSectionInput.disabled = false;
                                }
                            });
                        </script>

                        <!-- Test Case ID -->
                        <div class="form-group mb-3">
                            <label for="test_case_id" class="form-label">Test Case ID</label>
                            <input
                                type="text"
                                id="test_case_id"
                                name="test_case_id"
                                class="form-control border p-3"
                                placeholder="Enter test case ID"
                                value="{{ old('test_case_id') }}"
                                required
                            >
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control border p-3"
                                placeholder="Enter test case description"
                                required
                            >{{ old('description') }}</textarea>
                        </div>

                        <!-- Steps -->
                        <div class="form-group mb-3">
                            <label for="steps" class="form-label">Steps</label>
                            <textarea
                                id="steps"
                                name="steps"
                                class="form-control border p-3"
                                placeholder="Enter test steps"
                                required
                            >{{ old('steps') }}</textarea>
                        </div>

                        <!-- Expected Result -->
                        <div class="form-group mb-3">
                            <label for="expected_result" class="form-label">Expected Result</label>
                            <textarea
                                id="expected_result"
                                name="expected_result"
                                class="form-control border p-3"
                                placeholder="Enter expected result"
                                required
                            >{{ old('expected_result') }}</textarea>
                        </div>

                        <!-- Comments -->
                        <div class="form-group mb-3">
                            <label for="comments" class="form-label">Comments</label>
                            <textarea
                                id="comments"
                                name="comments"
                                class="form-control border p-3"
                                placeholder="Optional comments"
                            >{{ old('comments') }}</textarea>
                        </div>

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
