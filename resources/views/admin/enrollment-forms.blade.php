
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>DepEd Enrollment Forms</h2>
            <p class="text-muted mb-0">Manage enrollment PDF templates.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <strong>Upload Enrollment PDF Template</strong>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.enrollment-forms.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Form Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Example: SY 2026-2027"
                           value="{{ old('title') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Version Tag</label>
                    <input type="text"
                           name="version_tag"
                           class="form-control"
                           placeholder="Example: v2026"
                           value="{{ old('version_tag') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">PDF File</label>
                    <input type="file"
                           name="pdf_file"
                           class="form-control"
                           accept="application/pdf"
                           required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           id="is_active">
                    <label class="form-check-label" for="is_active">
                        Set as active template
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Upload PDF Template
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Uploaded Templates</strong>
        </div>

        <div class="card-body">
            @if($forms->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Version</th>
                                <th>Status</th>
                                <th>Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($forms as $form)
                                <tr>
                                    <td>{{ $form->title }}</td>
                                    <td>{{ $form->version_tag }}</td>
                                    <td>
                                        @if($form->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $form->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.enrollment-forms.mapping', $form->id) }}"
                                           class="btn btn-sm btn-primary">
                                            PDF Mapping
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No enrollment PDF templates uploaded yet.</p>
            @endif
        </div>
    </div>

</div>
@endsection
