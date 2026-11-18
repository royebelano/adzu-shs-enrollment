@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mag-upload ng DepEd Word Template (.docx)</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.deped.template.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Template Title</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="Hal. DepEd Enrollment Form 2026">
                        </div>

                        <div class="mb-3">
                            <label for="template_file" class="form-label">Word File (.docx)</label>
                            <input type="file" class="form-control" id="template_file" name="template_file" accept=".docx" required>
                        </div>

                        <button type="submit" class="btn btn-success">I-upload ang Template</button>
                    </form>
                </div>
            </div>

            <!-- Listahan ng mga naka-upload na templates -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Mga Naka-upload na Template</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Pamagat</th>
                                <th>Petsa ng Pag-upload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>{{ $template->title }}</td>
                                    <td>{{ $template->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Wala pang naka-upload na template.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
